<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 18.05.2015
 * Time: 7:46
 */

namespace Jungle\XPlate\Interfaces {


	use Jungle\Basic\INamed;

	/**
	 * Interface IWebEngine
	 * @package Jungle\XPlate\Interfaces
	 */
	interface IWebEngine extends INamed{

		/**
		 * @param $prefix
		 * @return $this
		 */
		public function setVendor($prefix);

		/**
		 * @return string
		 */
		public function getVendor();


		/**
		 * @param $key
		 * @param $value
		 */
		public function setOption($key,$value);

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getOption($key,$default = null);

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasOption($key);


	}
}