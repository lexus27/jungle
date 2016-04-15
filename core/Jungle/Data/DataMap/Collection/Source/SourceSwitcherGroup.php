<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 1:00
 */
namespace Jungle\Data\DataMap\Collection\Source {

	/**
	 * Class SourceSwitcherGroup
	 * @package Jungle\Data\DataMap\Collection\Source
	 */
	class SourceSwitcherGroup{

		/** @var  string */
		protected $current_alias;

		/** @var array - ['remote','local_db'] */
		protected $required_source_aliases = [];

		/** @var SourceSwitcher[] */
		protected $switchers = [];

		public function __construct($required){
			$this->required_source_aliases = $required;
		}

		/**
		 * @param $source_key
		 * @return bool
		 */
		public function switchTo($source_key){
			if(in_array($source_key,$this->required_source_aliases,true)){
				$this->current_alias = $source_key;
				foreach($this->switchers as $switcher){
					$switcher->switchTo($source_key);
				}
				return true;
			}
			return false;
		}

		/**
		 * @param SourceSwitcher $switcher
		 * @return $this
		 */
		public function addSwitcher(SourceSwitcher $switcher){
			$this->switchers[] = $switcher;
			return $this;
		}

	}
}

