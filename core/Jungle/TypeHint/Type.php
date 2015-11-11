<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:11
 */
namespace Jungle\TypeHint {

	use Jungle\Basic\INamedBase;

	/**
	 * Class Type
	 * @package Jungle\TypeHint
	 */
	abstract class Type implements INamedBase{

		/** @var string */
		protected $name;

		/**
		 * @param $name
		 */
		public function setName($name){$this->name = $name;}

		/**
		 * @return string
		 */
		public function getName(){return $this->name;}

		/**
		 * @param $value
		 * @param $passedTypeString
		 * @return bool
		 */
		abstract public function check($value, $passedTypeString);

		/**
		 * @param $typeString
		 * @return bool
		 */
		public function parse($typeString){
			return $typeString === $this->name;
		}

	}
}

