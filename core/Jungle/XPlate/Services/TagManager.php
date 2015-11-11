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
		Jungle\XPlate\HTML\Element\Tag;
	use Jungle\XPlate\Interfaces\IService;

	/**
	 * Class TagManager
	 * @package Jungle\XPlate\Services
	 */
	class TagManager extends Manager{

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			$this->dummySetAllowed(true);
			$this->caseSetInsensitive(false);
			parent::__construct('tags', $store);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(
					function (){
						return new Tag();
					}
				);
			}
			return parent::getFactory();
		}

		/**
		 * @param string $identifier
		 * @return Tag
		 */
		public function get($identifier){
			return parent::get($identifier);
		}

	}
}