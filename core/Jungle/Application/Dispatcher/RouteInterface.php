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

	use Jungle\Application\Dispatcher\Router\RoutingInterface;

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
		 * @return array [m,c,a]
		 */
		public function getDynamics();



	}
}

