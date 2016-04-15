<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 16:45
 */
namespace Jungle\User\Access\ABAC\Context {

	use Jungle\Smart\Value\IValue;
	use Jungle\Smart\Value\IValueErasable;
	use Jungle\Smart\Value\IValueSettable;

	/**
	 * Interface ISubstitute
	 * @package Jungle\User\Access\ABAC\Context
	 */
	interface ISubstitute extends IValue, IValueSettable, IValueErasable{

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

