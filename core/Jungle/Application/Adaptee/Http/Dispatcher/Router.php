<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 22:36
 */
namespace Jungle\Application\Adaptee\Http\Dispatcher {

	use Jungle\Application\Adaptee\Http\Dispatcher\Router\Route;
	use Jungle\Application\Dispatcher;
	use Jungle\Application\RequestInterface;
	use Jungle\RegExp\Template;

	/**
	 * Class Router
	 * @package Jungle\Application\Dispatcher\Router
	 */
	class Router extends \Jungle\Application\Dispatcher\Router{

		/**
		 * @param RequestInterface $request
		 * @return bool
		 */
		public function isDesiredRequest(RequestInterface $request){
			return $request instanceof \Jungle\Util\Specifications\Http\RequestInterface;
		}


		/**
		 * Create and add {Any} Method Route
		 * @param $pattern
		 * @param $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function any($pattern,array $options = null){
			$pattern_config = null;
			if(is_array($pattern)){
				if(isset($pattern['pattern'])){
					$pattern_config = $pattern['config'];
					$pattern = $pattern['pattern'];
				}elseif(isset($pattern[0]) && is_string($pattern[0])){
					$pattern_config = isset($pattern[1]) && is_array($pattern[1])?$pattern[1]:null;
					$pattern = $pattern[0];
				}else{
					throw new \LogicException('Route pattern is not valid definition!');
				}
			}
			$options = array_replace([
				'reference'     => null,
				'params'        => null,
				'name'          => null,
				'bindings'      => null,
				'modify'        => true,
			    'default'       => null,
			],(array)$options);
			$route = new Route();
			$route->setDefaultParam($options['default']);
			$route->setName($options['name']);
			$route->setPreferredMethod(null);
			$route->setPattern($pattern,(array)$pattern_config);
			$route->setDefaultReference(Dispatcher::normalizeReference($options['reference']));
			$route->setDefaultParams((array)$options['params']);
			$route->setBindings((array)$options['bindings'],true);
			$route->setModifyPath(boolval($options['modify']));
			$route->setRouter($this);
			$this->routes[] = $route;
			return $route;
		}

		/**
		 * @param $method
		 * @param $pattern
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function anyOf($method, $pattern,array $options = null){
			$route = $this->any($pattern, $options);
			$route->setPreferredMethod($method);
			return $route;
		}

		/**
		 * Create and add GET Method Route
		 * @param $pattern
		 * @param $options
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function get($pattern,array $options = null){
			$route = $this->any($pattern, $options);
			$route->setPreferredMethod('GET');
			return $route;
		}

		/**
		 * Create and add POST Method Route
		 * @param $pattern
		 * @param $options
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function post($pattern,array $options = null){
			$route = $this->any($pattern,$options);
			$route->setPreferredMethod('POST');
			return $route;
		}

		/**
		 * Create and add PUT Method Route
		 * @param $pattern
		 * @param $options
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function put($pattern,array $options = null){
			$route = $this->any($pattern,$options);
			$route->setPreferredMethod('PUT');
			return $route;
		}

		/**
		 * @param $pattern
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function patch($pattern,array $options = null){
			$route = $this->any($pattern, $options);
			$route->setPreferredMethod('PATCH');
			return $route;
		}

		/**
		 * @param $pattern
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function options($pattern,array $options = null){
			$route = $this->any($pattern, $options);
			$route->setPreferredMethod('OPTIONS');
			return $route;
		}

		/**
		 * @param $pattern
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function head($pattern,array $options = null){
			$route = $this->any($pattern, $options);
			$route->setPreferredMethod('HEAD');
			return $route;
		}

		/**
		 * Create and add DELETE Method Route
		 * @param $pattern
		 * @param array $options
		 * @return \Jungle\Application\Adaptee\Http\Dispatcher\Router\Route
		 */
		public function delete($pattern,array $options = null){
			$route = $this->any($pattern, $options);
			$route->setPreferredMethod('DELETE');
			return $route;
		}




		/**
		 * @param Template\Manager $mgr
		 */
		protected function _initializeTemplateManager(Template\Manager $mgr){
			parent::_initializeTemplateManager($mgr);
			$mgr->setPlaceholderDefaults($this->special_params_prefix . 'namespace',[
					'pattern' => '(?:[[:alpha:]][\w\-]*\w)(?:/[[:alpha:]][\w\-]*\w)*',
					'renderer' => function($value){
						return str_replace('.','/',$value);
					},
					'evaluator' => function($value){
						return str_replace('/','.',$value);
					}
			]);

			$mgr->setPlaceholderDefaults($this->special_params_prefix . 'parameters',[
					'type' => 'array',
					'setArguments' => [
							'delimiter' => '/'
					],
					'before' => '/'
			]);
		}


	}
}

