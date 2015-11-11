<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 14:55
 */

namespace Jungle\XPlate\CSS\Media {


	/**
	 * Class Expression
	 * @package Jungle\XPlate\CSS\Media
	 */
	class Query{

		/**
		 * @var bool
		 */
		protected $negated = false;

		/**
		 * @var Type
		 */
		protected $type;

		/**
		 * @var FnDecorator[]
		 */
		protected $combos = [];


		/**
		 * @param Type $type
		 * @return $this
		 */
		public function setType(Type $type){
			$this->type = $type;
			return $this;
		}

		/**
		 * @return Type
		 */
		public function getType(){
			return $this->type;
		}


		/**
		 * @param bool $negated
		 */
		public function setNegated($negated = true){
			$this->negated = boolval($negated);
		}

		/**
		 * @return bool
		 */
		public function isNegated(){
			return $this->negated;
		}


		/**
		 * @param FnDecorator $combo
		 * @return $this
		 */
		public function addCombo(FnDecorator $combo){
			$i = $this->searchCombo($combo);
			if($i === false){
				$this->combos[] = $combo;
			}
			return $this;
		}

		/**
		 * @param FnDecorator $combo
		 * @return mixed
		 */
		public function searchCombo(FnDecorator $combo){
			return array_search($combo,$this->combos,true);
		}

		/**
		 * @param FnDecorator $combo
		 * @return $this
		 */
		public function removeCombo(FnDecorator $combo){
			$i = $this->searchCombo($combo);
			if($i !== false){
				array_splice($this->combos,$i,1);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function toString(){
			$combos = implode(' and ',$this->combos);
			return ($this->negated?'not ':'').$this->type->getName().($combos?' and '. $combos:'');
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->toString();
		}


	}

}