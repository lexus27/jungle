<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 0:09
 */
namespace Jungle\XPlate\Services {

	use Jungle\Smart\Keyword\Factory;
	use Jungle\Smart\Keyword\Pool;
	use Jungle\Smart\Keyword\Storage;
	use Jungle\XPlate\CSS\Selector\Dependency;

	/**
	 * Class SelectorDependencyPool
	 * @package Jungle\XPlate\Services
	 * '>', '+', '~', ' '
	 */
	class SelectorDependencyPool extends Pool{

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			$this->dummySetAllowed(true);
			$this->caseSetInsensitive(false);
			parent::__construct('CSSSelectorDependencyManager', $store);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function (){
					return new Dependency();
				});
			}
			return parent::getFactory();
		}
		/**
		 * @param string $identifier
		 * @return Dependency
		 */
		public function get($identifier){
			return parent::get($identifier);
		}

	}
}

