<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.06.2016
 * Time: 18:07
 */
namespace Jungle\Di {

	use Jungle\Application;
	use Jungle\Application\Dispatcher;
	use Jungle\Application\RequestInterface as ApplicationRequestInterface;
	use Jungle\Application\ResponseInterface as ApplicationResponseInterface;
	use Jungle\Application\View\ViewStrategyInterface;
	use Jungle\Application\ViewInterface;
	use Jungle\Data\Record\Head\SchemaManager;
	use Jungle\Loader;
	use Jungle\Messenger;
	use Jungle\User\Account;
	use Jungle\User\SessionManager;
	use Jungle\Util\Specifications\Http\CookieManagerInterface;
	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;
	use Jungle\Util\Specifications\Http\ResponseSettableInterface;

	/**
	 * Class Injectable
	 * @package Jungle\Di
	 *
	 * @property Application $application
	 * @property Dispatcher $dispatcher
	 * @property \Jungle\Application\RouterInterface|\Jungle\Application\Strategy\Http\Router $router
	 * @property ApplicationRequestInterface|RequestInterface $request
	 * @property ApplicationResponseInterface|ResponseInterface|ResponseSettableInterface $response
	 * @property $cache
	 * @property $event
	 *
	 * @property ViewInterface $view
	 * @property ViewStrategyInterface $view_strategy
	 *
	 * @property $filesystem
	 * @property $database
	 * @property SchemaManager $schema
	 *
	 * @property Loader $loader
	 *
	 * @property Account $account
	 * @property $access
	 * @property SessionManager $session
	 * @property CookieManagerInterface $cookie
	 * @property Messenger $messenger
	 */
	class Injectable implements InjectionAwareInterface{

		/** @var  DiInterface */
		protected $_dependency_injector;

		/** @var bool  */
		protected static $_dependency_injector_cacheable = false;

		/** @var array  */
		protected $_dependency_injector_cache = [];

		/**
		 * @return DiInterface
		 */
		public function getDi(){
			return $this->_dependency_injector;
		}

		/**
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setDi(DiInterface $di){
			$this->_dependency_injector = $di;
			return $this;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function __get($name){
			if(static::$_dependency_injector_cacheable){
				if(!array_key_exists($name,$this->_dependency_injector_cache)){
					$result = $this->_dependency_injector->get($name);
					$this->_dependency_injector_cache[$name] = $result;
					return $result;
				}
				return $this->_dependency_injector_cache[$name];
			}else{
				if(!$this->_dependency_injector){
					throw new \LogicException('DependencyInjector is not supplied in object');
				}
				return $this->_dependency_injector->get($name);
			}
		}

	}
}

