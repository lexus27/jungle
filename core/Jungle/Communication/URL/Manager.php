<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 22:58
 */
namespace Jungle\Communication\URL {

	use Jungle\Communication\URL\Manager\PortPool;
	use Jungle\Communication\URL\Manager\SchemePool;
	use Jungle\Smart\Keyword\Storage;

	/**
	 * Class Manager
	 * @package Jungle\Communication\URL
	 */
	class Manager extends \Jungle\Smart\Keyword\Manager{


		/** @var Manager */
		protected static $default;

		/**
		 * @return Manager
		 */
		public static function getDefault(){
			if(!self::$default){
				self::$default = new self(Storage::getDummy());
			}
			return self::$default;
		}

		/**
		 * @param Manager $manager
		 */
		public static function setDefault(Manager $manager){
			self::$default = $manager;
		}

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			$this->addPool( (new PortPool($store)) );
			$this->addPool( (new SchemePool($store)) );
		}

	}
}

