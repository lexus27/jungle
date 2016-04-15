<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 2:28
 */

namespace Jungle\XPlate\CSS {

	use Jungle\XPlate\CSS\Selector\Group as SelectorGroup;

	/**
	 * Class Rule
	 * @package Jungle\XPlate\CSS
	 */
	class Rule implements IRule{

		/**
		 * @var SelectorGroup
		 */
		protected $selectorGroup;

		/**
		 * @var Definition
		 */
		protected $definition;


		/**
		 * @param SelectorGroup $set
		 * @return $this
		 */
		public function setSelectorGroup(SelectorGroup $set){
			$this->selectorGroup = $set;
			return $this;
		}

		/**
		 * @return SelectorGroup
		 */
		public function getSelectorGroup(){
			return $this->selectorGroup;
		}

		/**
		 * @param Definition $definition
		 * @return $this
		 */
		public function setDefinition(Definition $definition){
			$this->definition = $definition;
			return $this;
		}

		/**
		 * @return Definition
		 */
		public function getDefinition(){
			return $this->definition;
		}


	}
}