<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 3:26
 */

namespace Jungle\XPlate\CSS\Definition {

	use Jungle\Smart\Keyword\Keyword;
	use Jungle\XPlate\WebEngineSet;

	/**
	 * Class Property
	 * @package Jungle\XPlate\CSS\Definition
	 */
	class Property extends Keyword implements IProperty{

		/**
		 * @var bool
		 */
		protected $shorthand = false;

		/**
		 * @var IProperty
		 */
		protected $general;

		/**
		 * @var IProperty[]
		 */
		protected $contains = [];


		/**
		 * @var null
		 */
		protected $type = null;

		/**
		 * @var bool
		 */
		protected $typeStrict = false;


		/**
		 * @var bool
		 */
		protected $vendor_required = false;



		/**
		 * @return string
		 */
		public function getName(){
			return $this->getIdentifier();
		}

		/**
		 * @param $name
		 */
		public function setName($name){
			$this->setIdentifier($name);
		}

		/**
		 * @param IProperty $property
		 * @param bool $addContain
		 * @param bool $removeOld
		 * @return $this
		 */
		public function setGeneral(IProperty $property = null, $addContain = true,$removeOld = true){
			$old = $this->general;
			if($old !== $property){
				$this->general = $property;
				if($addContain && $property)$property->addContain($this,false);
				if($removeOld && $old) $old->removeContain($this,false);
			}
			return $this;
		}

		/**
		 * @return IProperty
		 */
		public function getGeneral(){
			return $this->general;
		}


		/**
		 * @param bool $required
		 * @return $this
		 */
		public function setVendorRequired($required = true){
			$this->vendor_required = $required;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isVendorRequired(){
			return boolval($this->vendor_required);
		}

		/**
		 * @param $value
		 * @return array
		 */
		public function processEval($value){
			$a = [];

			if($value instanceof IValue){
				$value = $value->processEval($this);
			}else{
				$value = "{$value}";
			}

			$value = "{$value}";
			$a[] = $this->getIdentifier() . ': '. $value.';';
			if($this->isVendorRequired() && ($vendors = WebEngineSet::getDefault()->getVendors())){
				foreach($vendors as $prefix){
					$a[] = '-'. $prefix .'-'.$this->getIdentifier() . ': '. $value.';';
				}
			}
			return $a;
		}

		/**
		 * @return mixed
		 */
		public function isShorthand(){
			return $this->shorthand;
		}

		/**
		 * @param bool $short
		 * @return $this
		 */
		public function setShorthand($short = true){
			$short = boolval($short);
			if($this->shorthand !== $short){
				$this->shorthand = $short;
				if($short){
					$this->setGeneral(null);
				}else{
					foreach($this->contains as $property){
						$property->setGeneral(null);
					}
					$this->contains = [];
				}
			}
			return $this;
		}


		/**
		 * @param IProperty $property
		 * @param bool $setGeneral
		 * @return $this
		 */
		public function addContain(IProperty $property,$setGeneral = true){
			if($this->searchContain($property)===false){
				$this->contains[] = $property;
				if($setGeneral)$property->setGeneral($this,false);
			}
			return $this;
		}

		/**
		 * @param IProperty $property
		 * @return mixed
		 */
		public function searchContain(IProperty $property){
			return array_search($property,$this->contains,true);
		}

		/**
		 * @param IProperty $property
		 * @return mixed
		 */
		public function removeContain(IProperty $property,$removeGeneral = true){
			if(($i = $this->searchContain($property)) !== false){
				unset($this->contains[$i]);
				if($removeGeneral) $property->setGeneral(null,true,false);
			}
			return $this;
		}

		/**
		 * @param string $raw_property_name with prefixes and them
		 * @return string normalized
		 */
		public static function normalizePropertyName($raw_property_name){
			$vendors = WebEngineSet::getDefault()->getVendors();
			$vendors = array_map(function($v){
				return preg_quote($v,'@');
			}, $vendors);
			return preg_replace('@^(-('.implode('|',$vendors).')-)?([\w\-]+)$@','$2',strtolower(trim($raw_property_name)));
		}

	}

}
