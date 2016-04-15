<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.03.2016
 * Time: 17:49
 */
namespace Jungle\Data {

	use Jungle\Data\Registry\RegistryReadInterface;

	/**
	 * Class Registry
	 * @package Jungle\Data
	 */
	class Registry implements RegistryReadInterface{

		/** @var array */
		protected $items = [];

		/** @var   */
		protected $reader;



		/**
		 * @param $key
		 * @return mixed|null
		 */
		public function get($key){
			if(isset($this->items[$key])){
				return $this->items[$key];
			}
			return null;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function has($key){
			return isset($this->items[$key]);
		}

		/**
		 * @param $key
		 * @param $value
		 */
		public function set($key, $value){
			$this->items[$key] = $value;
		}

		/**
		 * @param $key
		 */
		public function remove($key){
			unset($this->items[$key]);
		}


		/**
		 * @param $reader
		 * @return $this
		 */
		public function setReader($reader){
			$this->reader = $reader;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getReader(){
			return $this->reader;
		}

	}
}

