<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 25.06.2015
 * Time: 0:01
 */

namespace Jungle\XPlate{

	use Jungle\XPlate\Interfaces\IWebEngine;
	use Jungle\XPlate\Interfaces\IWebEngineSet;

	/**
	 * Class WebEngineSet
	 * @package Jungle\XPlate
	 *
	 * Набор Модификаций браузерных движков
	 * Webkit Moz MS
	 *
	 */
	class WebEngineSet implements IWebEngineSet{

		/**
		 * @var IWebEngineSet|null
		 */
		protected static $tmp;

		/**
		 * @var IWebEngineSet
		 */
		protected static $default;

		/**
		 * @var IWebEngine[]
		 */
		protected $engines;

		/**
		 * @param IWebEngineSet $set
		 */
		public static function setTemporal(IWebEngineSet $set=null){
			self::$tmp = $set;
		}

		/**
		 * @param IWebEngineSet $set
		 * @return mixed
		 * Выставить текущий набор
		 */
		public static function setDefault(IWebEngineSet $set){
			self::$default = $set;
		}

		/**
		 * @return IWebEngineSet
		 */
		public static function getDefault(){
			if(self::$tmp){
				return self::$tmp;
			}
			if(!self::$default){
				self::$default = new static();
			}
			return self::$default;
		}

		/**
		 * @param IWebEngine $engine
		 * @return $this
		 */
		public function addEngine(IWebEngine $engine){
			if($this->searchEngine($engine)===false){
				$this->engines[] = $engine;
			}
			return $this;
		}

		/**
		 * @param IWebEngine $engine
		 * @return bool|int
		 */
		public function searchEngine(IWebEngine $engine){
			return array_search($engine,$this->engines,true);
		}

		/**
		 * @param IWebEngine $engine
		 * @return $this
		 */
		public function removeEngine(IWebEngine $engine){
			if( ($i = $this->searchEngine($engine)) !== false){
				array_splice($this->engines,$i,1);
			}
			return $this;
		}

		/**
		 * @return Interfaces\IWebEngine[]
		 */
		public function getEngines(){
			return $this->engines;
		}

		/**
		 * @return string[]
		 */
		public function getVendors(){
			$vendors = [];
			foreach($this->engines as $engine){
				$vendor = $engine->getVendor();
				if(!in_array($vendor, $vendors, true)){
					$vendors[] = (string)$vendor;
				}
			}
			return array_filter($vendors,'');
		}

	}
}
