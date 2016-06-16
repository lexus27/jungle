<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.06.2016
 * Time: 14:39
 */
namespace Jungle\Data\Foundation\Registry {
	
	use Jungle\Data\Foundation\Registry;

	/**
	 * Class RegistryOverlay
	 * @package Jungle\Data\Foundation\Registry
	 */
	class RegistryOverlay extends Registry{

		/** @var  RegistryReadInterface */
		protected $parent;

		/**
		 * @param RegistryReadInterface $registryReadInterface
		 * @return $this
		 */
		public function setParent(RegistryReadInterface $registryReadInterface){
			$this->parent = $registryReadInterface;
			return $this;
		}

		/**
		 * @return RegistryReadInterface
		 */
		public function getParent(){
			return $this->parent;
		}


		/**
		 * @param $key
		 * @return bool|mixed
		 */
		public function has($key){
			return $this->_checkExists($this->items,$key) || ($this->parent && $this->parent->has($key));
		}

		/**
		 * @param $key
		 * @return null
		 */
		public function get($key){
			return $this->_checkExists($this->items,$key)?$this->items[$key]:($this->parent?$this->parent->get($key):null);
		}


	}
}

