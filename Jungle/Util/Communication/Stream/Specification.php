<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.01.2016
 * Time: 15:07
 */
namespace Jungle\Util\Communication\Stream {

	use Jungle\Util\Communication\Stream;


	/**
	 * Class CodeSpecification
	 * @package Jungle\Util\Communication\Stream
	 */
	class Specification{

		const CODE_UNKNOWN      = 0;

		const CODE_INFO         = 1;

		const CODE_SUCCESS      = 2;

		const CODE_POINTING     = 3;

		const CODE_WARNING      = 4;

		const CODE_FATAL        = 5;



		/** @var string[]|int[] */
		protected $codes = [];

		/** @var callable */
		protected $code_type_recognizer;

		/** @var callable */
		protected $code_recognizer;

		/** @var callable */
		protected $reader;

		/** @var callable */
		protected $command_structure_modifier;

		/** @var int  */
		protected $maxLength = 512;

		/**   */
		public function __construct(){
			static $defaultRecognizer;
			if($defaultRecognizer === null){
				$defaultRecognizer = function($code){
					if($code >= 100 && $code < 200){
						return self::CODE_INFO;
					}
					if($code >= 200 && $code < 300){
						return self::CODE_SUCCESS;
					}
					if($code >= 300 && $code < 400){
						return self::CODE_POINTING;
					}
					if($code >= 400 && $code < 500){
						return self::CODE_WARNING;
					}
					if($code >= 500 && $code < 600){
						return self::CODE_FATAL;
					}
					return self::CODE_UNKNOWN;
				};
			}
			$this->code_type_recognizer = $defaultRecognizer;
		}

		/**
		 * @param $modifier
		 */
		public function setCommandStructureModifier($modifier){
			$this->command_structure_modifier = $modifier;
		}

		/**
		 * @return callable
		 */
		public function getCommandStructureModifier(){
			return $this->command_structure_modifier;
		}


		/**
		 * @param callable  $reader
		 */
		public function setReader(callable $reader=null){
			$this->reader = $reader;
		}

		/**
		 * @return callable
		 */
		public function getReader(){
			return $this->reader;
		}

		/**
		 * @param callable|null $recognizer
		 * @return $this
		 */
		public function setCodeTypeRecognizer(callable $recognizer = null){
			$this->code_type_recognizer = $recognizer;
			return $this;
		}

		/**
		 * @param callable|null $recognizer
		 * @return $this
		 */
		public function setCodeRecognizer(callable $recognizer = null){
			$this->code_recognizer = $recognizer;
			return $this;
		}

		/**
		 * @param $code
		 * @return int
		 */
		public function getCodeType($code){
			return call_user_func($this->code_type_recognizer,$code);
		}

		/**
		 * @param $modifier
		 * @param $command
		 * @return mixed|string
		 */
		public static function modifyCommandText($modifier, $command){
			if(is_callable($modifier)){
				return call_user_func($modifier,$command);
			}elseif(is_array($modifier)){
				if(isset($modifier[0]) && $modifier[0] && isset($modifier[1])){
					return preg_replace('/'.addcslashes($modifier[0],'/').'/'.(isset($modifier[2])?$modifier[2]:''),$modifier[1],$command);
				}
				if(isset($modifier['preg_replace']) && is_array($modifier['preg_replace'])){
					return preg_replace($modifier['preg_replace'][0],$modifier['preg_replace'][1],$command);
				}
				$modified = $command;
				if($modifier['suffix']){
					$modified  = $modified . $modifier['suffix'];
				}
				if($modifier['prefix']){
					$modified  = $modifier['prefix'] . $modified;
				}
				return $modified;
			}else{
				return $command;
			}
		}

		/**
		 * @param $data
		 * @return mixed
		 */
		public function getCode($data){
			if(!$this->code_recognizer){
				throw new \LogicException('Code recognizer is not set in specification');
			}
			return call_user_func($this->code_recognizer,$data);
		}

		/**
		 * @param $code
		 * @param $status
		 */
		public function setCode($code, $status){
			$this->codes[$code] = $status;
		}

		/**
		 * @param $code
		 * @return $this
		 */
		public function removeCode($code){
			unset($this->codes[$code]);
			return $this;
		}

		/**
		 * @param $code
		 * @return bool
		 */
		public function hasCode($code){
			return isset($this->codes[$code]);
		}

		/**
		 * @param $code
		 * @param null $default
		 * @return int|null|string
		 */
		public function getStatus($code, $default = null){
			return isset($this->codes[$code])?$this->codes[$code]:$default;
		}

		/**
		 * @param $definition
		 * @return Stream
		 */
		public function openStream($definition){
			$stream = Builder::getDefault()->buildStream($definition);
			$stream->setSpecification($this);
			return $stream;
		}

		/**
		 * @param array $definition
		 * @return Specification
		 */
		public static function fromArray(array $definition){
			$spec = new Specification();
			if(isset($definition['code_type_recognizer'])){
				$spec->setCodeTypeRecognizer($definition['code_type_recognizer']);
			}
			if(isset($definition['command_modifier'])){
				$spec->setCommandStructureModifier($definition['command_modifier']);
			}
			if(isset($definition['code_recognizer'])){
				$spec->setCodeRecognizer($definition['code_recognizer']);
			}
			if(isset($definition['reader'])){
				$spec->setReader($definition['reader']);
			}
			if(is_array($definition['codes'])){
				foreach($definition['codes'] as $code => $status){
					$spec->setCode($code,$status);
				}
			}
			return $spec;
		}

		/**
		 * @return array
		 */
		public function toArray(){
			return [
				'code_type_recognizer'  => $this->code_type_recognizer,
				'code_recognizer'       => $this->code_recognizer,
				'reader'                => $this->reader,
				'codes'                 => $this->codes
			];
		}

		/**
		 * @return int
		 */
		public function getMaxLength(){
			return $this->maxLength;
		}

		/**
		 * @param int $maxLength
		 */
		public function setMaxLength($maxLength = 512){
			$this->maxLength = $maxLength;
		}

	}
}

