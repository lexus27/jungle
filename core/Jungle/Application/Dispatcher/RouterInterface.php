<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:17
 */
namespace Jungle\Application\Dispatcher {

	use Jungle\Application\Dispatcher\Context;
	use Jungle\Application\Dispatcher\Router\RoutingInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\RegExp\Template;

	/**
	 * Interface RouterInterface
	 * @package Jungle\Application
	 */
	interface RouterInterface{

		/**
		 * @param RequestInterface $request
		 * @return mixed
		 */
		public function isDesiredRequest(RequestInterface $request);

		/**
		 * @param RequestInterface $request
		 * @return RoutingInterface
		 */
		public function match(RequestInterface $request);

		/**
		 * @param $reference
		 * @param array $params
		 * @return string
		 */
		public function generateLink($params = null, $reference = null);

		/**
		 * @param $route_alias
		 * @param null|array $params
		 * @param null $reference
		 * @return string
		 */
		public function generateLinkBy($route_alias, $params = null,$reference = null);

		/**
		 * @return Template
		 */
		public function getTemplateManager();

		/**
		 * @return string
		 */
		public function getSpecialParamPrefix();

		/**
		 * @param string $path
		 * @return string
		 */
		public function modifyPath($path);

		/**
		 * @param $route
		 * @param $reference
		 * @param $routing
		 * @return mixed
		 */
		public function beforeRouteMatched($route, $reference, $routing);

		/**
		 * @param callable $checker
		 * @return mixed
		 */
		public function setBeforeRouteMatchedChecker(callable $checker);

	}
}

