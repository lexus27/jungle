<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 3:13
 */

namespace Jungle\XPlate\CSS\Selector {

	use Jungle\XPlate\CSS\Selector;

	/**
	 * Class SelectorSet
	 * @package Jungle\XPlate\CSS
	 */
	class Group{

		/**
		 * @var Selector[]
		 */
		protected $selectors = [];

		/**
		 * @param Selector $s
		 * @return $this
		 */
		public function addSelector(Selector $s){
			$i = $this->searchSelector($s);
			if($i === false){
				$this->selectors[] = $s;
			}
			return $this;
		}

		/**
		 * @param Selector $s
		 * @return mixed
		 */
		public function searchSelector(Selector $s){
			return array_search($s, $this->selectors, true);
		}

		/**
		 * @param Selector $s
		 * @return $this
		 */
		public function removeSelector(Selector $s){
			$i = $this->searchSelector($s);
			if($i !== false){
				array_splice($this->selectors, $i, 1);
			}
			return $this;
		}

	}
}