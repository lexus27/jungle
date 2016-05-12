<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.05.2016
 * Time: 23:27
 */
namespace Jungle\Application\Dispatcher\Controller {

	/**
	 * Interface FormatterAwareInterface
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	interface FormatterAwareInterface{

		/**
		 * @return FormatterInterface|null
		 */
		public function getFormatter();

	}
}

