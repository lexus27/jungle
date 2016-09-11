<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 27.04.2015
 * Time: 22:24
 */

namespace Jungle\XPlate\Services {


	use Jungle\Util\Smart\Keyword\Factory;
	use Jungle\Util\Smart\Keyword\Pool;
	use Jungle\Util\Smart\Keyword\Storage;
	use Jungle\XPlate\HTML\Element\Attribute;
	use Jungle\XPlate\Interfaces\IService;

	/**
	 * Class StylePool
	 * @package Jungle\XPlate\Services
	 */
	class StylePool extends Pool{

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			$this->dummySetAllowed(true);
			$this->caseSetInsensitive(false);
			parent::__construct('cssProperties', $store);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function (){
					return new Attribute();
				});
			}
			return parent::getFactory();
		}
		/**
		 * @param string $identifier
		 * @return Attribute
		 */
		public function get($identifier){
			return parent::get($identifier);
		}

	}
}