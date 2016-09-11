<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.09.2016
 * Time: 11:41
 */
namespace Jungle\Di {

	/***
	 * Class DiNestingTrait
	 * @package Jungle\Di
	 */
	trait DiNestingTrait{

		/** @var  DiNestingInterface|DiLocatorInterface|DiNestingOverlappingInterface|DiSettingInterface */
		protected $parent;


		/**
		 * @param DiNestingInterface|DiLocatorInterface|DiNestingOverlappingInterface|DiSettingInterface $parent
		 * @return $this
		 */
		public function setParent($parent){
			if($this->parent !== $parent){
				$this->parent = $parent;
			}
			return $this;
		}

		/**
		 * @return DiNestingInterface|DiLocatorInterface|DiNestingOverlappingInterface|DiSettingInterface
		 */
		public function getParent(){
			return $this->parent;
		}

		/**
		 * @return DiNestingInterface|DiLocatorInterface|DiNestingOverlappingInterface|DiSettingInterface
		 */
		public function getRoot(){
			if(!$this->parent){
				return $this;
			}
			return $this->parent->getRoot();
		}
	}
}
