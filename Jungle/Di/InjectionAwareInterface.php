<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.04.2016
 * Time: 23:52
 */
namespace Jungle\Di {

	/**
	 * Interface InjectionAwareInterface
	 * @package Jungle\Di
	 */
	interface InjectionAwareInterface{

		/**
		 * @return DiInterface
		 */
		public function getDi();

		/**
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setDi(DiInterface $di);

		/**
		 * @return DiInterface|DiNestingOverlappingInterface|DiSettingInterface
		 */
		public function getAttachedDi();

	}
}

