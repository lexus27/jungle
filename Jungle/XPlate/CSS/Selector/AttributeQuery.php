<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 0:08
 */

namespace Jungle\XPlate\CSS\Selector {


	use Jungle\XPlate\CSS\Selector\AttributeQuery\Checker;
	use Jungle\XPlate\Interfaces\IHtmlAttribute;

	/**
	 * Class AttributeQuery
	 * @package Jungle\XPlate\CSS\Selector
	 */
	class AttributeQuery{

		/**
		 * @var string
		 */
		protected $attribute;

		/**
		 * @var Checker
		 */
		protected $checker;

		/**
		 * @var mixed
		 */
		protected $collated;


		/**
		 * @param IHtmlAttribute|string $attribute
		 * @return $this
		 */
		public function setAttribute($attribute){
			$this->attribute = $attribute instanceof IHtmlAttribute?$attribute->getName():$attribute;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAttribute(){
			return $this->attribute;
		}

		/**
		 * @param Checker $checker
		 * @return $this
		 */
		public function setChecker(Checker $checker){
			$this->checker = $checker;
			return $this;
		}

		/**
		 * @return Checker
		 */
		public function getChecker(){
			return $this->checker;
		}

		/**
		 * @param $value
		 * @return $this
		 */
		public function setCollated($value){
			$this->collated = $value;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getCollated(){
			return $this->collated;
		}

		/**
		 * @param $value
		 * @return bool
		 */
		public function check($value){
			return $this->checker->check($value, $this->collated);
		}


		public function __toString(){
			return '[' . $this->getAttribute() . $this->getChecker() . '=' . $this->getCollated() . ']';
		}


	}
}
