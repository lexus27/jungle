<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:17
 */
namespace Jungle\Application\Router {

	use Jungle\Application\RouterInterface;

	/**
	 * Interface RouteInterface
	 * @package Jungle\Application
	 */
	interface RouteInterface{

		/**
		 * @param string $name
		 * @return $this
		 */
		public function setName($name);

		/**
		 * @return string
		 */
		public function getName();


		/**
		 * @return RouterInterface
		 */
		public function getRouter();

		/**
		 * @param RouterInterface $router
		 * @return $this
		 */
		public function setRouter(RouterInterface $router);

		/**
		 * @param $parameter
		 * @return $this
		 */
		public function setDefaultParam($parameter);

		/**
		 * @return mixed
		 */
		public function getDefaultParam();

		/**
		 * @param string $pattern
		 * @param array $options
		 * @return mixed
		 */
		public function setPattern($pattern,array $options = []);

		/**
		 * @return string
		 */
		public function getPattern();

		/**
		 * @return string
		 */
		public function getPatternOptions();


		/**
		 * @return mixed
		 */
		public function getDefaultReference();

		/**
		 * @return array
		 */
		public function getDefaultParams();

		/**
		 * @param null $params
		 * @param null $reference
		 * @return mixed
		 */
		public function tryLink($params = null, $reference = null);

		/**
		 * @param array $params
		 * @param array|mixed $reference
		 * @return string
		 */
		public function generateLink($params = null, $reference = null);

		/**
		 * @param RoutingInterface $routing
		 * @return void
		 */
		public function match(RoutingInterface $routing);

		/**
		 * @param string $path
		 * @return string
		 */
		public function modifyPath($path);

		/**
		 * @return bool
		 */
		public function isDynamic();

		/**
		 * @return array [m,c,a]
		 */
		public function getDynamics();

		/**
		 * @param $normalizeReference
		 * @return mixed
		 */
		public function setDefaultReference($normalizeReference);

		/**
		 * @param array $params
		 * @return $this
		 */
		public function setDefaultParams(array $params = []);
		/**
		 * @param array $bindings
		 * @param bool|true $merge
		 * @return $this
		 */
		public function setBindings(array $bindings, $merge = false);

		/**
		 * @param bool|true $modifyAllowed
		 * @return $this
		 */
		public function setModifyPath($modifyAllowed = true);

		/**
		 * @param callable $converter
		 * @return mixed
		 */
		public function setConverter(callable $converter = null);

		/**
		 * @return mixed
		 */
		public function getConverter();

	}
}

