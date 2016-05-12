<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.05.2016
 * Time: 21:02
 */
namespace Jungle\Application\Dispatcher\Router {
	
	use Jungle\Application\Dispatcher\RouteInterface;
	use Jungle\Application\Dispatcher\Router\Exception\MatchedException;
	use Jungle\Application\Dispatcher\RouterInterface;
	use Jungle\Application\RequestInterface;

	/**
	 * Interface RoutingInterface
	 * @package Jungle\Application\Dispatcher\Router
	 */
	interface RoutingInterface{

		/**
		 * @param RouteInterface $route
		 * @param array|null $params
		 * @param mixed $reference
		 * @param bool $throwException
		 * @return $this
		 * @throws MatchedException
		 */
		public function matched(RouteInterface $route,array $params = null, $reference = null, $throwException = true);

		/**
		 * @param array|null $params
		 * @param mixed $reference
		 * @param bool $throwException
		 * @return $this
		 * @throws MatchedException
		 */
		public function notFound(array $params = null, $reference = null, $throwException = true);

		/**
		 * @return bool
		 */
		public function isNotFound();

		/**
		 * @return bool
		 */
		public function isUnknown();

		/**
		 * @return RequestInterface
		 */
		public function getRequest();

		/**
		 * @return RouteInterface
		 */
		public function getRoute();

		/**
		 * @return RouterInterface
		 */
		public function getRouter();

		/**
		 * @return array
		 */
		public function getParams();

		/**
		 * @return array|mixed|null
		 */
		public function getReference();

	}
}

