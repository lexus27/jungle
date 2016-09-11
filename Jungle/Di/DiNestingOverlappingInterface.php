<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.09.2016
 * Time: 11:35
 */
namespace Jungle\Di {


	/**
	 * Interface DiNestingOverlappingInterface
	 * @package Jungle\Di
	 */
	interface DiNestingOverlappingInterface{

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

