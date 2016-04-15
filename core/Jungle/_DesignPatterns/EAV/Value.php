<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 7:08
 */
namespace Jungle\_DesignPatterns\EAV {

	/**
	 * Можно считать этот класс как представление KeyPair ,
	 * почти как Keyword только Keyword является аттрибутом,
	 * но Keyword связывается со значением только из вне
	 * Class Value
	 * @package Jungle\_DesignPatterns\EAV
	 */
	class Value{

		/**
		 * @var Attribute
		 */
		protected $attribute;

		/**
		 * @var mixed
		 */
		protected $value;


		/** @Constructor
		 * @param Attribute $attribute
		 * @param $value
		 */
		public function __construct(Attribute $attribute, $value){
			$this->setAttribute($attribute)->setValue($value);
		}


		/**
		 * @param Attribute $attribute
		 * @return $this
		 */
		public function setAttribute(Attribute $attribute){
			$this->attribute = $attribute;
			return $this;
		}

		/**
		 * @return Attribute
		 */
		public function getAttribute(){
			return $this->attribute;
		}

		/**
		 * @param $value
		 * @return $this
		 */
		public function setValue($value){
			$this->value = $value;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getValue(){
			return $this->value;
		}

	}
}

