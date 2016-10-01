<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.01.2016
 * Time: 13:00
 */
namespace Jungle\Util\Communication\Stream {

	use Jungle\Util\Communication\Stream;
	use Jungle\Util\Communication\Stream\Command\Rule;

	/**
	 * Class Command
	 * @package Jungle\Util\Communication\Stream
	 */
	class Command{

		/**
		 * @var Specification
		 */
		protected $specification;

		/** @var string|null */
		protected $command;

		/** @var string|null */
		protected $response;

		/** @var \Jungle\Util\Communication\Stream\Specification|\Jungle\Util\Communication\Stream\Command\Rule[] */
		protected $rules = [];

		/** @var int|string */
		protected $code;

		/** @var callable|array|null */
		protected $modifier;

		/** @var array[] */
		protected $notifications = [];

		/** @var null|int */
		protected $status = null;

		/**
		 * @param callable|array|null $modifier
		 * @return $this
		 */
		public function setModifier($modifier = null){
			$this->modifier = $modifier;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getModifier(){
			return $this->modifier;
		}

		/**
		 * @param array $notifications
		 * @return $this
		 */
		public function setNotifications(array $notifications){
			$this->notifications = $notifications;
			return $this;
		}

		/**
		 * @param $type
		 * @param $message
		 * @param $rule
		 * @param $instruction
		 */
		public function addNotify($type, $message, $rule, $instruction){
			$this->notifications[] = [$type,$message,$rule, $instruction];
		}

		public function getStatus(){
			return $this->status;
		}

		public function isSuccess(){
			return $this->status === 'success';
		}

		public function isNotified(){
			return $this->status === 'notified';
		}

		public function isFailure(){
			return $this->status === 'failure';
		}

		/**
		 * @return \array[]
		 */
		public function getNotifications(){
			return $this->notifications;
		}


		/**
		 * @param $command
		 * @return $this
		 */
		public function setCommand($command){
			$this->command = $command;
			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getCommand(){
			return $this->command;
		}

		/**
		 * @param $response
		 * @return $this
		 */
		public function setResponse($response){
			$this->response = $response;
			return $this;
		}

		public function isMutable(){
			return !((bool)$this->command);
		}

		/**
		 * @return mixed|null|string
		 */
		public function represent(){
			if(!$this->isMutable()){
				$modified = Specification::modifyCommandText($this->modifier,$this->command);
				$m = $this->specification->getCommandStructureModifier();
				if($m) $modified = Specification::modifyCommandText($m,$modified);
				return $modified;
			}
			return null;
		}

		/**
		 * @return mixed|null|string
		 */
		public function __toString(){
			return (string)$this->represent();
		}

		/**
		 * @return null|string
		 */
		public function getResponse(){
			return $this->response;
		}

		/**
		 * @return int|string
		 */
		public function getCode(){
			return $this->code;
		}

		/**
		 * @param $code
		 * @return $this
		 */
		public function setCode($code){
			$this->code = $code;
			return $this;
		}

		/**
		 * @param \Jungle\Util\Communication\Stream\Command\Rule $rule
		 * @return $this
		 */
		public function addRule(Rule $rule){
			if($this->searchRule($rule)===false){
				$this->rules[] = $rule;
			}return $this;
		}

		/**
		 * @param \Jungle\Util\Communication\Stream\Command\Rule $rule
		 * @return mixed
		 */
		public function searchRule(Rule $rule){
			return array_search($rule,$this->rules,true);
		}

		/**
		 * @param \Jungle\Util\Communication\Stream\Specification|\Jungle\Util\Communication\Stream\Command\Rule $rule
		 * @return $this
		 */
		public function removeRule(Rule $rule){
			if(($i = $this->searchRule($rule)) !==false){
				array_splice($this->rules,$i,1);
			}return $this;
		}

		/**
		 * @return bool|mixed
		 * @throws Exception
		 */
		public function check(){
			$s = $this->specification;
			$this->notifications = [];
			$code   = $this->getCode();
			$instruction      = [
				'check_default' => true
			];
			foreach($this->rules as $rule){
				try{
					$behaviourReturn    = null;
					$matched            = $rule->match($code);
					$behaviour          = $rule->getBehaviour();
					$msg                = $rule->getErrorMessage();
					$type               = $s::CODE_FATAL;

					if(is_callable($behaviour)){
						$i = call_user_func($behaviour, $s, $this, $matched , $rule);
						if(is_array($i)){
							if(isset($i['check_default']) ){
								$instruction['check_default'] = $i['check_default'];
							}
							if(isset($i['suppress'])){
								$instruction['suppress'] = $i['suppress'];
								break;
							}
						}elseif($i === false){
							$instruction['cancel'] = true;
							$this->status = 'stopped';
							return;
						}

					}elseif(is_array($behaviour)){
						if(isset($behaviour['check_default']) ){
							$instruction['check_default'] = $behaviour['check_default'];
						}
						if(isset($behaviour['suppress'])){
							$instruction['suppress'] = $behaviour['suppress'];
							break;
						}
					}

					if(!$matched){
						if($msg){
							throw new Exception($msg, $type);
						}elseif(is_string($behaviour)){
							throw new Exception($msg, $type);
						}else{
							throw new Exception($s->getStatus($code,'Command response error'), $s->getCodeType($code));
						}
					}

				}catch(Exception $e){
					$this->addNotify($e->getCode(), $e->getMessage(), $rule, $instruction);
					if($e->getCode() === $s::CODE_FATAL){
						$this->status = 'failure';
						throw $e;
					}else{
						$this->status = 'notified';
					}
				}
			}
			if($instruction['check_default']){
				$msg    = $s->getStatus($code);
				$type   = $s->getCodeType($code);
				if($type === $s::CODE_FATAL){
					$this->status = 'failure';
					throw new Exception($msg,$type);
				}
			}
			$this->status = 'success';
		}

		/**
		 * @return bool
		 */
		public function isExecuted(){
			return (bool)$this->status;
		}

		public function reset(){
			$this->code             = null;
			$this->response         = null;
			$this->notifications    = [];
			$this->status           = null;
		}

		/**
		 *
		 */
		public function __clone(){
			$this->reset();
		}

		/**
		 * @param Specification $spec
		 */
		public function setSpecification(Specification $spec){
			$this->specification = $spec;
		}

	}
}

