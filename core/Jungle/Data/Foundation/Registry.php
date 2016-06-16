<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.06.2016
 * Time: 12:22
 */
namespace Jungle\Data\Foundation {

	use Jungle\Data\Foundation\Registry\RegistryReadInterface;

	/**
	 * Class Registry
	 * @package Jungle\Data\Foundation
	 */
	abstract class Registry implements RegistryReadInterface{

		/** @var array  */
		protected $items = [];

		/** @var bool  */
		protected $check_key_exists = true;

		/**
		 * @param $key
		 * @return mixed
		 */
		public function get($key){
			return $this->_checkExists($this->items,$key)?$this->items[$key]:null;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function has($key){
			return $this->_checkExists($this->items,$key);
		}

		/**
		 * @param array|\ArrayAccess $array
		 * @param $key
		 * @return bool
		 */
		protected function _checkExists( $array, $key){
			return $this->check_key_exists?array_key_exists($key,$array):isset($array[$key]);
		}


	}
}

