<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.10.2016
 * Time: 19:01
 */
namespace Jungle\Application\Reporter {

	/**
	 * Class ExceptionReporterInterface
	 * @package Jungle\Application\Reporter
	 */
	interface ExceptionReporterInterface{


		/**
		 * @param \Exception $exception
		 */
		public function report(\Exception $exception);

	}
}

