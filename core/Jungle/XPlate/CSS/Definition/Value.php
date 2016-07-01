<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 3:26
 */

namespace Jungle\XPlate\CSS\Definition {

	use Jungle\XPlate\CSS\Definition;
	use Jungle\XPlate\Interfaces\IProperty;
	use Jungle\XPlate\Interfaces\IValue;

	/**
	 * Class Value
	 * @package Jungle\XPlate\CSS\Definition
	 */
	class Value implements IValue{

		/**
		 * @param IProperty $property
		 * @return array
		 */
		public function processEval(IProperty $property){

		}

		/**
		 * @return mixed
		 */
		public function getValue(){

		}

		/**
		 * @param $value
		 * @return $this
		 */
		public function setValue($value){

		}

		/**
		 * @param \Jungle\Util\Smart\Value\IValue|mixed $value
		 * @return bool
		 */
		public function equal($value){

		}
	}
}
