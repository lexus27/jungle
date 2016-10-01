<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.04.2016
 * Time: 19:07
 */
namespace Jungle\RegExp\Template {

	use Jungle\RegExp\Pattern;
	use Jungle\RegExp\Template;
	use Jungle\RegExp\Type;
	use Jungle\Util\NamedInterface;
	use Jungle\Util\Value\String;

	/**
	 * Class Placeholder
	 */
	class Placeholder implements NamedInterface{

		/** @var  Template */
		protected $template;

		/** @var  string */
		protected $name;

		/** @var  string|null */
		protected $name_regex;

		/** @var  Type */
		protected $type;

		/** @var  array|null */
		protected $type_arguments;


		/** @var  string|null */
		protected $custom_pattern;

		/** @var  null|callable */
		protected $custom_evaluator;

		/** @var  null|callable */
		protected $custom_renderer;


		/** @var  string */
		protected $before;

		/** @var  string */
		protected $after;

		/** @var  bool */
		protected $optional = false;

		/** @var  mixed */
		protected $default = null;

		/** @var array  */
		protected $options = [];


		/**
		 * Placeholder constructor.
		 * @param Template $ownTemplate
		 * @param $type
		 * @param array $type_arguments
		 * @param array $options
		 */
		public function __construct(Template $ownTemplate, $type,array $type_arguments = [], array $options = []){
			$this->template         = $ownTemplate;
			$this->options          = $options;
			$this->setType($type,$type_arguments);
		}

		/**
		 * @param $after
		 * @return $this
		 */
		public function setAfter($after){
			$this->after = $after;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAfter(){
			return $this->after;
		}

		/**
		 * @return string
		 */
		protected function getAfterPattern(){
			return preg_quote($this->after,'@');
		}

		/**
		 * @param $before
		 * @return $this
		 */
		public function setBefore($before){
			$this->before = $before;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getBefore(){
			return $this->before;
		}

		/**
		 * @return string
		 */
		protected function getBeforePattern(){
			return preg_quote($this->before,'@');
		}

		/**
		 * @param bool|true $optional
		 * @return $this
		 */
		public function setOptional($optional = true, $default = null){
			$this->optional = $optional;
			$this->default = $default;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isOptional(){
			return $this->optional;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getOption($key, $default = null){
			return isset($this->options[$key])?$this->options[$key]:$default;
		}

		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if($this->name !== $name){
				$this->name = $name;
				$this->name_regex = null;
			}
			return $this;
		}

		/**
		 * @param null $pattern
		 * @param callable|null $evaluator
		 * @param callable|null $renderer
		 * @return $this
		 * @throws Placeholder\Exception
		 */
		public function setCustom($pattern = null, callable $evaluator = null, callable $renderer = null){
			if(($evaluator||$renderer) && $this->typeCallAsDynamic()){
				throw new Template\Placeholder\Exception('Placeholder type "'.$this->name.'" is dynamic call, Renderer or Evaluator not allowed for current placeholder!');
			}
			if($pattern && $this->typeCallAsDynamic()){
				throw new Template\Placeholder\Exception('Placeholder type "'.$this->name.'" is dynamic call, custom pattern is not allowed!');
			}
			if($pattern && (!$evaluator||!$renderer) && $this->type->isDynamic()){
				throw new Template\Placeholder\Exception('Placeholder type "'.$this->name.'" is dynamic call, but defined a custom pattern , evaluator and renderer required!');
			}
			$this->custom_pattern = $pattern;
			$this->custom_evaluator = $evaluator;
			$this->custom_renderer = $renderer;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCustomPattern(){
			return $this->custom_pattern;
		}

		/**
		 * @return string
		 * @throws Placeholder\Exception
		 */
		public function getPattern(){
			if($this->custom_pattern){
				if($this->type->isDynamic() && (!$this->custom_renderer || !$this->custom_evaluator)){
					throw new Template\Placeholder\Exception('Placeholder type "'.$this->name.'" is dynamic, but is defined custom pattern');
				}
				return $this->custom_pattern;
			}else{
				return $this->type->getPattern($this->type_arguments);
			}
		}

		/**
		 * @param $typeName
		 * @param array $arguments
		 * @return $this
		 * @throws Placeholder\Exception
		 */
		public function setType($typeName, array $arguments = []){
			$type = $this->template->getManager()->getType($typeName);
			if(!$type){
				throw new Template\Placeholder\Exception('Type "'.$typeName.'" not exists!');
			}
			$this->type = $type;
			if($arguments){
				if($this->type->isDynamic()){
					$this->setTypeArguments($arguments);
				}else{
					throw new Template\Placeholder\Exception('Указаный тип "'.$typeName.'" не может быть вызван динамически! '.var_export($arguments,true));
				}
			}
			return $this;
		}

		/**
		 * @return Type
		 */
		public function getType(){
			return $this->type;
		}


		/**
		 * @param array $arguments
		 * @return $this
		 */
		public function setTypeArguments(array $arguments = []){
			$this->type_arguments = $arguments;
			return $this;
		}

		/**
		 * @param array $named_arguments
		 */
		public function replaceTypeArguments(array $named_arguments){
			$interface = $this->type->getArgumentsInterface();
			foreach($named_arguments as $index => $value){
				if(is_string($index)){
					foreach($interface as $int => $i){
						if($i['name'] === $index){
							$this->type_arguments[$int] = $value;
						}
					}
				}else{
					$this->type_arguments[$index] = $value;
				}
			}
		}

		/**
		 * @return array
		 */
		public function getTypeArguments(){
			return $this->type_arguments;
		}


		/**
		 * @return string
		 */
		public function compile(){
			if($this->after || $this->before){
				$regex = "(?:{$this->getBeforePattern()}(?<{$this->getNameRegex()}>{$this->getPattern()}){$this->getAfterPattern()})";
			}else{
				if($this->isIndexed()){
					$regex = "({$this->getPattern()})";
				}else{
					$regex = "(?<{$this->getNameRegex()}>{$this->getPattern()})";
				}
			}
			return $regex.($this->optional?'?':'');
		}

		/**
		 * @return array|null
		 */
		public function typeCallAsDynamic(){
			return $this->type_arguments;
		}


		/**
		 * @param string $value
		 * @return bool
		 */
		public function isValid($value){
			if(!$value && $this->optional){
				return true;
			}
			if($this->custom_pattern){
				return Pattern::validateValue($this->custom_pattern, $value,'Su');
			}else{
				return $this->type->validate($value, $this->type_arguments);
			}
		}

		/**
		 * Привести строкове представление значение в реальное значение php
		 * @param string $value
		 * @return mixed
		 * @throws Placeholder\Exception
		 */
		public function evaluate($value){
			if(!$value){
				if($this->optional){
					if($this->default){
						return $this->default;
					}else{
						$vartype = $this->type->getVartype();
						if('array' === $vartype){
							return [];
						}
						if('object' === $vartype){
							return new \stdClass();
						}
						return $this->default;
					}
				}else{
					throw new Template\Placeholder\Exception('Evaluate error: Value "'.$this->name.'" is empty');
				}
			}
			if(!$this->type_arguments && $this->custom_evaluator){
				return call_user_func($this->custom_evaluator,$value);
			}else{
				return $this->type->evaluate($value,$this->type_arguments);
			}
		}

		/**
		 * @param mixed $value
		 * @return string
		 * @throws Placeholder\Exception
		 */
		public function render($value){
			if(!$value){
				if($this->optional){
					if(!$this->default || !$this->getOption('default_render',true)){
						return '';
					}elseif($this->default){
						$value = $this->default;
					}
				}else{
					throw new \LogicException('Render error: Value "'.$this->name.'" is empty');
				}
			}
			if(!$this->type_arguments && $this->custom_renderer){
				$val = call_user_func($this->custom_renderer,$value);
			}else{
				$val = $this->type->render($value, $this->type_arguments);
			}
			if(!$this->isValid($val)){
				$passed_type = gettype($value);
				if(is_object($value)){
					$passed_type = get_class($value);
				}
				throw new Template\Placeholder\Exception(
						'Render invalid, is not compatible with pattern: Placeholder "'.$this->getName().'" = "'.$val.'" '.
						'passed: (type: "'.$passed_type.'", rendered: "'.$val.'") '.
						'must be: (type: "'.$this->type->getVartype().'", pattern: "'.$this->getPattern().'")'

				);
			}
			return $this->getBefore().$val.$this->getAfter();
		}

		/**
		 * @return mixed
		 */
		public function getNameRegex(){
			if($this->name_regex===null){
				$this->name_regex = 'ph_'.String::crc32Alnum($this->name);
			}
			return $this->name_regex;
		}

		/**
		 * @return bool
		 */
		public function isIndexed(){
			return is_numeric($this->name);
		}



	}

}

