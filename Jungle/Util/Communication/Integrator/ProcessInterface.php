<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:06
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Interface ProcessInterface
	 * @package Jungle\Util\Communication\Integrator
	 */
	interface ProcessInterface{

		/**
		 * @return MethodInterface
		 */
		public function getMethod();

		/**
		 * @return bool
		 */
		public function isCanceled();

	}
}

