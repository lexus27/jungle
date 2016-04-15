<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 19:13
 */
namespace Jungle\XPlate\Services {

	use Jungle\Smart\Keyword\Factory;
	use Jungle\Smart\Keyword\Pool;
	use Jungle\Smart\Keyword\Storage;
	use Jungle\XPlate\CSS\Selector\Marker\Cls;

	/**
	 * Class MarkerClassPool
	 * @package Jungle\XPlate\Services
	 */
	class MarkerClassPool extends Pool{

		public function __construct(){
			$this->caseSetInsensitive(true);
			parent::__construct('MarkerClassPool', Storage::getDummy());
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function (){
					return new Cls();
				});
			}
			return parent::getFactory();
		}
		/**
		 * @param string $identifier
		 * @return Cls
		 */
		public function get($identifier){
			return parent::get($identifier);
		}

	}
}

