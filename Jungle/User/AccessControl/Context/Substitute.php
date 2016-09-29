<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 16:45
 */
namespace Jungle\User\AccessControl\Context {

	use Jungle\Util\Smart\Value\IValue;
	use Jungle\Util\Value\Massive;

	/**
	 *
	 * Класс реализует подмену целевого объекта контекста.
	 * Применим для:
	 *  Подмена свойств или типов самого объекта при проверках до непосредственного доступа к реальному объекту
	 *  сбор Query из Conditions|Expressions при проверках до непосредственного доступа к реальному объекту
	 *
	 *
	 * Class Substitute
	 * @package Jungle\User\AccessControl\Context
	 */
	class Substitute implements SubstituteInterface{

		/** @var string class Name */
		protected $class;

		/** @var string var type */
		protected $type;

		/** @var int count */
		protected $count;

		/** @var int length */
		protected $length;

		/** @var mixed value if not substitute */
		protected $value;

		/** @var bool value defined */
		protected $value_defined = false;


		/**
		 * @param $class_name
		 * @return $this
		 */
		public function setClass($class_name){
			$this->class = $class_name;
			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getClass(){
			return $this->value_defined?get_class($this->value):$this->class;
		}


		/**
		 * @param $var_type
		 * @return $this
		 */
		public function setType($var_type){
			$this->type = $var_type;
			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getType(){
			return $this->value_defined?gettype($this->value):$this->type;
		}


		/**
		 * @param $count
		 * @return $this
		 */
		public function setCount($count){
			$this->count = $count;
			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getCount(){
			return $this->value_defined?count($this->value):$this->count;
		}


		/**
		 * @param $length
		 * @return $this
		 */
		public function setLength($length){
			$this->length = $length;
			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getLength(){
			return $this->value_defined?strlen($this->value):$this->length;
		}




		/**
		 * @param $value
		 * @return $this
		 */
		public function setValue($value){
			$this->value = $value;
			$this->value_defined = true;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getValue(){
			return $this->value;
		}

		/**
		 * @return bool
		 */
		public function eraseValue(){
			$this->value_defined = false;
			$this->value         = null;
			return true;
		}

		/**
		 * @param IValue|mixed $value
		 * @return bool
		 */
		public function equal($value){
			return $value instanceof IValue?$value->equal($this->value):$this->value === $value;
		}

		/**
		 * @return bool
		 */
		public function isDefined(){
			return $this->value_defined;
		}




		/**
		 * @param $info_key array range ['class','type','length','count' ]
		 * @param $value SubstituteInterface|mixed
		 * @return int|null|string
		 */
		public static function infoQuery($info_key, $value){
			if($value instanceof SubstituteInterface){
				switch($info_key){
					case 'class' :   return $value->getClass();
					case 'type'  :    return $value->getType();
					case 'length':  return $value->getLength();
					case 'count' :   return $value->getCount();
				}
			}else{
				switch($info_key){
					case 'class':   return get_class($value);
					case 'type':    return gettype($value);
					case 'length':  return mb_strlen($value);
					case 'count':   return count($value);
				}
			}
			return null;
		}

		/**
		 * @param array $info
		 * @param null $value
		 * @return static
		 */
		public static function release(array $info, $value=null){
			$s = new static();
			foreach(Massive::leaveKeys(array_change_key_case($info,CASE_LOWER), ['class','type','length','count'],true) as $k => $v){
				$s->{'set'.$k}($v);
			}
			if($value!==null)$s->setValue($value);
			return $s;
		}

	}
}

