<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 0:10
 */
namespace Jungle\XPlate\Services {

	use Jungle\Smart\Keyword\Factory;
	use Jungle\Smart\Keyword\Pool;
	use Jungle\Smart\Keyword\Storage;
	use Jungle\XPlate\CSS\Selector\AttributeQuery\Checker;

	/**
	 * Class SelectorAttributeCheckerPool
	 * @package Jungle\XPlate\Services
	 * '~=', '^=', '$=', '!=', '*='
	 * 
	 */
	class SelectorAttributeCheckerPool extends Pool{

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			$this->dummySetAllowed(true);
			$this->caseSetInsensitive(false);
			parent::__construct('CSSSelectorAttributeCheckerManager', $store);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function (){
					return new Checker();
				});
			}
			return parent::getFactory();
		}
		/**
		 * @param string $identifier
		 * @return Checker
		 */
		public function get($identifier){
			return parent::get($identifier);
		}

	}
}

