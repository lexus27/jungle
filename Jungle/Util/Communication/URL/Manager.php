<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 13:35
 */
namespace Jungle\Util\Communication\URL {

	use Jungle\Util\Communication\URL\Manager\PortPool;
	use Jungle\Util\Communication\URL\Manager\SchemePool;
	use Jungle\Util\Smart\Keyword\Storage;

	/**
	 * Class Manager
	 * @package Jungle\Communication\URL
	 */
	class Manager extends \Jungle\Util\Smart\Keyword\Manager{
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