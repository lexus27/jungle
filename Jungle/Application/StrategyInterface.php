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
	use Jungle\Util\INamed;

	/**
	 * Interface StrategyInterface
	 * @package Jungle\Application
	 */
	interface StrategyInterface extends DiInterface, INamed{

		/**
		 * @param $type
		 * @return mixed
		 */
		public function setName($type);

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param ResponseInterface $response
		 * @param ViewInterface $view
		 */
		public function complete(ResponseInterface $response, ViewInterface $view);

		/**
		 * @param DiInterface $root
		 */
		public function registerServices(DiInterface $root);

		/**
		 * @param RequestInterface $request
		 * @return bool
		 */
		public static function check(RequestInterface $request);

	}
}

