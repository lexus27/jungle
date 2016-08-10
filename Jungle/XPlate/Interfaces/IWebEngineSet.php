<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 24.06.2015
 * Time: 23:58
 */

namespace Jungle\XPlate\Interfaces{

	/**
	 * Class IWebEngineSet
	 * @package Jungle\XPlate\Interfaces
	 *
	 */
	interface IWebEngineSet {

		/**
		 * @param IWebEngineSet $set
		 * @return mixed
		 */
		public static function setDefault(IWebEngineSet $set);

		/**
		 * @return IWebEngineSet
		 */
		public static function getDefault();

		/**
		 * @param IWebEngineSet $set
		 */
		public static function setTemporal(IWebEngineSet $set=null);



		/**
		 * @param IWebEngine $engine
		 * @return $this
		 */
		public function addEngine(IWebEngine $engine);

		/**
		 * @param IWebEngine $engine
		 * @return bool|int
		 */
		public function searchEngine(IWebEngine $engine);

		/**
		 * @param IWebEngine $engine
		 * @return $this
		 */
		public function removeEngine(IWebEngine $engine);

		/**
		 * @return IWebEngine[]
		 */
		public function getEngines();

		/**
		 * @return string[]
		 */
		public function getVendors();

	}
}
