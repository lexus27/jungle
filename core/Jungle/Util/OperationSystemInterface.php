<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 13:43
 */
namespace Jungle\Util {

	/**
	 * Interface OperationSystemInterface
	 * @package Jungle\Util
	 */
	interface OperationSystemInterface{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getVersion();

		/**
		 * @return string
		 */
		public function getCapacity();
	}
}

