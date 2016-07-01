<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 19:13
 */
namespace Jungle\XPlate\Services {

	use Jungle\Util\Smart\Keyword\Factory;
	use Jungle\Util\Smart\Keyword\Pool;
	use Jungle\Util\Smart\Keyword\Storage;
	use Jungle\XPlate\CSS\Selector\Marker\Identifier;

	/**
	 * Class MarkerIdPool
	 * @package Jungle\XPlate\Services
	 */
	class MarkerIdPool extends Pool{

		public function __construct(){
			$this->caseSetInsensitive(true);
			parent::__construct('MarkerIdPool', Storage::getDummy());
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function (){
					return new Identifier();
				});
			}
			return parent::getFactory();
		}
		/**
		 * @param string $identifier
		 * @return Identifier
		 */
		public function get($identifier){
			return parent::get($identifier);
		}

	}
}

