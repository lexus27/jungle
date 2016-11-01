<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 16:45
 */
namespace Jungle\User\AccessControl\Context {

	use Jungle\Util\Smart\Value\IValue;
	use Jungle\Util\Smart\Value\IValueErasable;
	use Jungle\Util\Smart\Value\IValueSettable;

	/**
	 * Interface SubstituteInterface
	 * @package Jungle\User\AccessControl\Context\Context
	 */
	interface SubstituteInterface extends IValue, IValueSettable, IValueErasable{

		/**
		 * @param $class_name
		 * @return mixed
		 */
		public function setClass($class_name);
		public function getClass();


		/**
		 * @param $var_type
		 * @return mixed
		 */
		public function setType($var_type);
		public function getType();


		/**
		 * @param $count
		 * @return mixed
		 */
		public function setCount($count);
		public function getCount();

		/**
		 * @param $length
		 * @return mixed
		 */
		public function setLength($length);
		public function getLength();
		/**
		 * @return bool
		 */
		public function isDefined();







	}
}

