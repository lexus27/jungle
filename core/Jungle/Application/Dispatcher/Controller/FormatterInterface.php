<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.05.2016
 * Time: 23:26
 */
namespace Jungle\Application\Dispatcher\Controller {
	
	use Jungle\Application\RequestInterface;

	/**
	 * Interface FormatterInterface
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	interface FormatterInterface{

		/**
		 * @param RequestInterface $request
		 * @return mixed
		 */
		public function isDesiredRequest(RequestInterface $request);

		/**
		 * @param $data
		 * @return mixed
		 */
		public function format($data);

	}
}

