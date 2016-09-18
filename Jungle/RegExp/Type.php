<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.04.2016
 * Time: 0:59
 */
namespace Jungle\RegExp {

	use Jungle\RegExp\Type\Manager;
	use Jungle\RegExp\Type\TypeContainer;
	use Jungle\Util\Value;

	/**
	 * Предполагается использование класса как набросок регулярного выражения,
	 * который определяет тип данных, заданый в настройках:
	 *
	 * * * Использование в Jungle.RegExp.Template:
	 *
	 * 		{Native.In} 		-> {Analyzable} -> {Native.Out}
	 * 				AND {Native.In} === {Native.Out}
	 *
	 * 		{Analyzable.In} 	-> {Native} 	-> {Analyzable.In}
	 * 				AND {Analyzable.In} === {Analyzable.Out} (Бывают исключения в случае использования default)
	 *
	 * Шаблон проверяет Анализируемую строчку.
	 * Шаблон разбирает Анализируемую срочку(Analyzable), на Родные Данные(Native) по Плейсхолдерам
	 *
	 * Родные Данные(Native) могут быть конвертированы обратно в Анализируемую строчку(Analyzable),
	 * Как и любой набор данных соответствующих правилам Плейсхолдеров(Data Interface)
	 *
	 * DataInterface Определяется шаблоном - после отрисовки(в Analyzable) данные проверяются шаблоном,
	 * Результатом ошибки может быть исключения о несоответствии шаблона плейсхолдера
	 * с конвертированым значением этого плейсхолдера
	 *
	 * * * * * *
	 *
	 * Class Type
	 * @package Jungle\RegExp
	 */
	class Type implements TypeInterface{

		/** @var Manager */
		protected $registry;

		/** @var  TypeContainer */
		protected $parent ;

		/** @var  string|array */
		protected $name;

		/** @var  string|callable */
		protected $pattern;

		/** @var  string */
		protected $vartype;

		/** @var  null|callable */
		protected $evaluator;

		/** @var  null|callable */
		protected $renderer;

		/** @var  bool  */
		protected $arguments_support = false;

		/** @var array  */
		protected $arguments_interface = [];

		/** @var bool  */
		protected $arguments_has_required = false;

		/**
		 * Type constructor.
		 * @param $name
		 * @param callable|string $pattern
		 * @param string $vartype
		 *
		 * @param callable|null $renderer
		 * @param callable|null $evaluator
		 * @param bool $arguments_support
		 */
		public function __construct($name = null, $pattern = null, $vartype = 'string',callable $renderer = null,callable $evaluator = null, $arguments_support = false){
			if($name){
				$this->setName($name);
				$this->setPattern($pattern);
				$this->setVartype($vartype);
				$this->setRenderer($renderer);
				$this->setArgumentsSupport($arguments_support);
			}
		}

		/**
		 * @param $name
		 * @return Type|null
		 */
		public function getSubType($name){
			if(substr($name,0,1) === '&'){
				$name = substr($name,1);
				if(!$this instanceof TypeContainer){
					throw new \LogicException('Type is not container for start at this type');
				}
				$pattern = $this->getType($name);
			}else{
				$pattern = $this->registry->getType($name);
			}
			if($pattern){
				if($pattern->isDynamic()){
					throw new \LogicException('SubPattern "' . $name . '" is dynamic!');
				}
				return [$pattern->getPattern(),$pattern->getVartype()];
			}else{
				throw new \LogicException('SubPattern ' . $name . ' not exists!');
			}
		}

		/**
		 * @param $name
		 * @return string
		 */
		public function getSubPattern($name){
			if(substr($name,0,1) === '&'){
				$name = substr($name,1);
				if(!$this instanceof TypeContainer){
					throw new \LogicException('Type is not container for start at this type');
				}
				$pattern = $this->getType($name);
			}else{
				$pattern = $this->registry->getType($name);
			}
			if($pattern){
				if($pattern->isDynamic()){
					throw new \LogicException('SubPattern "' . $name . '" is dynamic!');
				}
				return $pattern->getPattern();
			}else{
				throw new \LogicException('SubPattern ' . $name . ' not exists!');
			}
		}

		/**
		 * @param $name
		 * @return string
		 */
		public function getSubVartype($name){
			if(substr($name,0,1)==='&'){
				$name = substr($name,1);
				if(!$this instanceof TypeContainer){
					throw new \LogicException('Type is not container for start at this type');
				}
				$pattern = $this->getType($name);
			}else{
				$pattern = $this->registry->getType($name);
			}
			if($pattern){
				if($pattern->isDynamic()){
					throw new \LogicException('SubType "'.$name.'" is dynamic!');
				}
				return $pattern->getVartype();
			}else{
				return 'string';
			}
		}

		/**
		 * @param bool|array $supported
		 * @return $this
		 */
		public function setArgumentsSupport($supported = null){
			$this->arguments_support = (bool)$supported;
			if(is_array($supported)){
				foreach($supported as $index => $argument_name){
					list($name, $vartype, $default) = array_replace([null,null,null],explode(':',$argument_name,3));
					$name = trim($name);
					$vartype = trim($vartype);
					$optional = false;
					if(!$vartype){
						$vartype = 'string';
					}
					if(!is_null($default)){
						$default = Value::actualStringRepresentType($default);
						$optional = true;
					}else{
						$this->arguments_has_required = true;
					}
					$supported[$index] = [
						'name'      => $name,
						'vartype'   => $vartype,
						'default'   => $default,
						'optional'  => $optional
					];

				}
				$this->arguments_interface = $supported;
			}else{
				$this->arguments_interface = [];
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasRequiredArguments(){
			return $this->arguments_has_required;
		}

		/**
		 * @param array $arguments
		 * @return array
		 */
		public function actualizeArguments(array $arguments = []){
			$args = [];
			foreach($this->arguments_interface as $index => $interface){
				if(!array_key_exists($index,$arguments)){
					if($interface['optional']){
						$args[$index] = $interface['default'];
					}else{
						throw new \LogicException('Type "'.$this->getName().'" have required argument "'.$interface['name'].'" is not passed!');
					}
				}else{
					if($interface['vartype'] === 'mixed'){
						$args[$index] = $arguments[$index];
					}else{
						$value = $arguments[$index];
						settype($value, $interface['vartype']);
						$args[$index] = $value;
					}
				}
			}
			return $args;
		}

		/**
		 * @return array
		 */
		public function getArgumentsInterface(){
			return $this->arguments_interface;
		}

		/**
		 * @return bool
		 */
		public function isArgumentsSupport(){
			return $this->arguments_support;
		}

		/**
		 * @return bool
		 */
		public function isDynamic(){
			return ($this->evaluator || $this->renderer || is_callable($this->pattern)) && $this->arguments_support;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function isName($name){
			return is_array($this->name)?in_array($name,$this->name,true):$this->name === $name;
		}

		/**
		 * @param string|array $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return is_array($this->name)?$this->name[0]:$this->name;
		}

		/**
		 * @return string
		 */
		public function getNames(){
			return $this->name;
		}

		/**
		 * @param string $pattern
		 * @return $this
		 */
		public function setPattern($pattern){
			$this->pattern = $pattern;
			return $this;
		}

		/**
		 * @param array $arguments
		 * @return string
		 */
		public function getPattern(array $arguments = null){
			if(is_callable($this->pattern)){
				if($this->arguments_support){
					$arguments = $this->actualizeArguments((array)$arguments);
					array_unshift($arguments, $this);
				}else{
					$arguments = [];
				}
				return call_user_func_array($this->pattern, $arguments);
			}
			return $this->pattern;
		}

		/**
		 * Vartype - системный тип данных
		 * @param string|callable $vartype
		 * @return $this
		 */
		public function setVartype($vartype){
			$this->vartype = $vartype;
			return $this;
		}
		
		/**
		 * @return string
		 */
		public function getVartype(){
			return $this->vartype;
		}


		/**
		 * Evaluator - Обозначает как привести строковое значение в системный
		 * @param null|callable $evaluator
		 * @return $this
		 */
		public function setEvaluator(callable $evaluator = null){
			$this->evaluator = $evaluator;
			return $this;
		}

		/**
		 * @return null|callable
		 */
		public function getEvaluator(){
			return $this->evaluator;
		}

		/**
		 * Renderer - обозначает как привести значение в строковое представление
		 * @param callable|null $renderer
		 * @return $this
		 */
		public function setRenderer(callable $renderer = null){
			$this->renderer = $renderer;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getRenderer(){
			return $this->renderer;
		}

		/**
		 * Проверка строкового значения на соответствие внутреннему шаблону
		 * @param $value
		 * @param array $arguments
		 * @return bool
		 */
		public function validate($value, array $arguments = null){
			$pattern = $this->getPattern($arguments);
			return Pattern::validateValue($pattern, $value,'S');
		}

		/**
		 * Приведение исходного значения в строковый тип
		 * @param $value
		 * @param array $arguments
		 * @return string
		 */
		public function render($value, array $arguments = null){
			if($this->renderer){
				if($this->arguments_support){
					$arguments = $this->actualizeArguments((array)$arguments);
					array_unshift($arguments, $this);
					array_unshift($arguments, $value);
				}else{
					$arguments = [$value];
				}
				$result = (string)call_user_func_array($this->renderer,$arguments);
			}else{
				$result = (string)$value;
			}
			return $result;
		}

		/**
		 * Evaluating value from string to work system value
		 * @param $value
		 * @param array $arguments
		 * @return mixed
		 */
		public function evaluate($value, array $arguments = null){
			if(is_callable($this->evaluator)){
				if($this->arguments_support){
					$arguments = $this->actualizeArguments((array)$arguments);
					array_unshift($arguments, $this);
					array_unshift($arguments, $value);
				}else{
					$arguments = [$value];
				}
				return call_user_func_array($this->evaluator,$arguments);
			}else{
				settype($value,$this->vartype);
				return $value;
			}
		}

		/**
		 * @param Manager $r
		 * @return $this
		 */
		public function setRegistry(Manager $r){
			$this->registry = $r;
			return $this;
		}

		/**
		 * @return Manager
		 */
		public function getRegistry(){
			return $this->registry;
		}

		/**
		 * @param TypeContainer|null $parent
		 * @return $this
		 */
		public function setParent(TypeContainer $parent = null){
			$this->parent = $parent;
			return $this;
		}

		/**
		 * @return \Jungle\RegExp\Type\TypeContainer
		 */
		public function getParent(){
			return $this->parent;
		}

	}
}

