<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.07.2016
 * Time: 23:04
 */
namespace Jungle\Application {

	use Jungle\Di\DiInterface;

	/**
	 * Interface StrategyInterface
	 * @package Jungle\Application
	 */
	interface StrategyInterface extends DiInterface{

		/**
		 * @return string
		 */
		public function getType();

		/**
		 * @param RequestInterface $request
		 * @return bool
		 */
		public function check(RequestInterface $request);

		/**
		 * @param ResponseInterface $response
		 * @param ViewInterface $view
		 */
		public function complete(ResponseInterface $response, ViewInterface $view);

	}
}

