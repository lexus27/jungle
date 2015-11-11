<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 0:10
 */
namespace Jungle\XPlate\Services {

	use Jungle\Smart\Keyword\Factory;
	use Jungle\Smart\Keyword\Manager;
	use Jungle\Smart\Keyword\Storage;
	use Jungle\XPlate\CSS\Selector\Modifier;

	/**
	 * Class SelectorModifierManager
	 * @package Jungle\XPlate\Services
	 * ':hover',
	 * ':visited',
	 * ':focus',
	 * '::first-child',
	 * '::last-child',
	 * '::after',
	 * '::before'
	 */
	class SelectorModifierManager extends Manager{

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			$this->dummySetAllowed(true);
			$this->caseSetInsensitive(true);
			parent::__construct('CSSSelectorModifierManager', $store);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function (){
					return new Modifier();
				});
			}
			return parent::getFactory();
		}
		/**
		 * @param string $identifier
		 * @return Modifier
		 */
		public function get($identifier){
			return parent::get($identifier);
		}

	}
}

