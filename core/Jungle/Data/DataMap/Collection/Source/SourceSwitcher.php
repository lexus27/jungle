<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 0:59
 */
namespace Jungle\Data\DataMap\Collection\Source {

	use Jungle\Data\DataMap\Collection\Source;

	/**
	 * Class SourceSwitcher
	 * @package Jungle\Data\DataMap\Collection\Source
	 */
	class SourceSwitcher extends Source{

		/** @var  SourceSwitcherGroup|null */
		protected $group;

		/** @var  array  */
		protected $sources = [];

		/** @var  string */
		protected $current_alias;

		/**
		 * @param $alias
		 * @param Source $source
		 * @return $this
		 */
		public function setSwitch($alias, Source $source){
			$this->sources[$alias] = $source;
			return $this;
		}

		/**
		 * @param $alias
		 * @return null
		 */
		public function getSwitch($alias){
			return isset($this->sources[$alias])?$this->sources[$alias]:null;
		}

		/**
		 * @param $alias
		 * @return bool
		 */
		public function hasSwitch($alias){
			return isset($this->sources[$alias]);
		}

		/**
		 * @param $source_alias
		 * @return bool
		 */
		public function switchTo($source_alias){
			$this->current_alias = $source_alias;
			return true;
		}

		public function getActiveSource(){
			return $this->sources[$this->current_alias];
		}

	}
}

