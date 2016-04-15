<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.04.2016
 * Time: 18:55
 */
namespace Jungle\Data\Registry {

	use Jungle\Basic\INamed;
	use Jungle\Data\Registry;

	/**
	 * Class NamedRegistry
	 * @package Jungle\Data\Registry
	 */
	class NamedRegistry extends Registry{

		protected $case_insensitive = false;

		/** @var  INamed[] */
		protected $items = [];

		/**
		 * @param $key
		 * @return mixed
		 */
		public function get($key){
			foreach($this->items as $iNamed){
				if(strcmp($iNamed->getName(), $key)){
					return $iNamed;
				}
			}
			return null;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function has($key){
			// TODO: Implement has() method.
		}

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function set($key, $value){
			// TODO: Implement set() method.
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function remove($key){
			// TODO: Implement remove() method.
		}

		/**
		 * @param INamed $INamed
		 */
		public function add(INamed $INamed){
			$this->items[] = $INamed;
		}
	}
}

