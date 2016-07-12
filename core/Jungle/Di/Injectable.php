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
	use Jungle\Application\Dispatcher\RouterInterface;
	use Jungle\Application\RequestInterface as ApplicationRequestInterface;
	use Jungle\Application\ResponseInterface as ApplicationResponseInterface;
	use Jungle\Application\ViewInterface;
	use Jungle\Data\Record\Head\SchemaManager;
	use Jungle\Loader;
	use Jungle\Messenger;
	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;
	use Jungle\Util\Specifications\Http\ResponseSettableInterface;

	/**
	 * Class Injectable
	 * @package Jungle\Di
	 *
	 * @property Application $application
	 * @property Dispatcher $dispatcher
	 * @property RouterInterface|\Jungle\Application\Adaptee\Http\Dispatcher\Router $router
	 * @property ApplicationRequestInterface|RequestInterface $request
	 * @property ApplicationResponseInterface|ResponseInterface|ResponseSettableInterface $response
	 * @property $cache
	 * @property $event
	 * @property ViewInterface $view
	 *
	 * @property $filesystem
	 * @property $database
	 * @property SchemaManager $schema
	 *
	 * @property Loader $loader
	 *
	 * @property $user
	 * @property $access
	 * @property $session
	 * @property Messenger $messenger
	 */
	class Injectable implements InjectionAwareInterface{

		/** @var  DiInterface */
		protected $_dependency_injector;

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
			if(!$this->_dependency_injector){
				throw new \LogicException('DependencyInjector is not supplied in object');
			}
			return $this->_dependency_injector->get($name);
		}

	}
}

