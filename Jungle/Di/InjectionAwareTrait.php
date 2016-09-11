<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.09.2016
 * Time: 20:59
 */
namespace Jungle\Di {

	/**
	 * Class InjectionAwareTrait
	 * @package Jungle\Di
	 */
	trait InjectionAwareTrait{

		/** @var  DiInterface | DiNestingOverlappingInterface | DiSettingInterface | HolderChains */
		protected $_dependency_injection;


		/**
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setDi(DiInterface $di){
			$this->_dependency_injection = $di;
			return $this;
		}

		/**
		 * @return DiInterface|DiNestingOverlappingInterface|DiSettingInterface | HolderChains
		 */
		public function getDi(){
			return $this->_dependency_injection->getRoot();
		}

		/**
		 * @return DiInterface|DiNestingOverlappingInterface|DiSettingInterface
		 */
		public function getAttachedDi(){
			return $this->_dependency_injection;
		}

	}
}
