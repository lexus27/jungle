<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.05.2016
 * Time: 21:02
 */
namespace Jungle\Application\Router {
	
	use Jungle\Application\RequestInterface;
	use Jungle\Application\Router\Exception\MatchedException;
	use Jungle\Application\RouterInterface;

	/**
	 * Interface RoutingInterface
	 * @package Jungle\Application\Router
	 */
	interface RoutingInterface{

		/**
		 * @param RouteInterface $route
		 * @param array|null|\Closure $params
		 * @param mixed $reference
		 * @param bool $throwException
		 * @return $this
		 * @throws MatchedException
		 */
		public function matched(RouteInterface $route,$params = null, $reference = null, $throwException = true);

		/**
		 * @param array|\Closure|null $params
		 * @param mixed $reference
		 * @param bool $throwException
		 * @return $this
		 * @throws MatchedException
		 */
		public function notFound($params = null, $reference = null, $throwException = true);

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

		/**
		 * @return $this
		 */
		public function reset();

	}
}

