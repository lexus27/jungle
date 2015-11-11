<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 27.04.2015
 * Time: 19:44
 */

namespace Jungle\XPlate\Services {


	use Jungle\Smart\Keyword\Factory,
		Jungle\Smart\Keyword\Manager,
		Jungle\Smart\Keyword\Storage,
		Jungle\XPlate\HTML\Element\Attribute;
	use Jungle\XPlate\Interfaces\IService;

	/**
	 * Class AttributeManager
	 * @package Jungle\XPlate2\Services
	 */
	class AttributeManager extends Manager{

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			$this->dummySetAllowed(true);
			$this->caseSetInsensitive(false);
			parent::__construct('attributes', $store);
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