<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.09.2016
 * Time: 10:52
 */
namespace Jungle\Di {
	
	/**
	 * Interface DiNestingInterface
	 * @package Jungle\Di
	 */
	interface DiNestingInterface{


		/**
		 * @param DiLocatorInterface|DiNestingInterface|DiSettingInterface $parent
		 * @return mixed
		 */
		public function setParent($parent);

		/**
		 * @return DiLocatorInterface|DiNestingInterface|DiSettingInterface
		 */
		public function getParent();

		/**
		 * @return DiLocatorInterface|DiNestingInterface|DiSettingInterface
		 */
		public function getRoot();


		/**
		 * @param $existingServiceKey
		 * @param null $definition
		 * @return $this
		 */
		public function setOverlapFrom($existingServiceKey, $definition = null);
		
		/**
		 * @param bool|false|string $overlap
		 * @return $this
		 */
		public function useSelfOverlapping($overlap = false);
		
		/**
		 * @return bool
		 */
		public function isSelfOverlapping();
		
		/**
		 * @return string
		 */
		public function getOverlapKey();


	}
}

