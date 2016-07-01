<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 05.05.2015
 * Time: 2:31
 */

namespace Jungle\XPlate\CSS {

	use Jungle\Util\ObjectStorage;
	use Jungle\XPlate\CSS\Definition\Property;
	use Jungle\XPlate\Interfaces\IProperty;

	/**
	 * Class Definition
	 * @package Jungle\XPlate\CSS
	 *
	 * Определение
	 *
	 */
	class Definition implements IDefinition{

		/**
		 * @var ObjectStorage|Property[]
		 */
		protected $properties;

		/**
		 *
		 */
		protected function checkProperties(){
			if(!$this->properties instanceof ObjectStorage){
				$this->properties = new ObjectStorage();
			}
		}

		/**
		 * @param $property
		 * @param $value
		 */
		public function setProperty($property, $value){
			$this->checkProperties();
			if(!$property instanceof IProperty){

			}
			$this->properties->has($property);

		}

		/**
		 * @param $property
		 */
		public function hasProperty($property){

		}

		/**
		 * @param $property
		 */
		public function unsetProperty($property){

		}

	}
}