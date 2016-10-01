<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 17:13
 */
namespace Jungle {
	
	use Jungle\Application\Dispatcher;
	use Jungle\Application\RequestInterface;
	use Jungle\Application\ResponseInterface;
	use Jungle\Application\Strategy\Cli\Router as CLI_Router;
	use Jungle\Application\Strategy\Http;
	use Jungle\Application\Strategy\Http\Router as HTTP_Router;
	use Jungle\Application\View;
	use Jungle\Application\View\ViewStrategy;
	use Jungle\Application\ViewInterface;
	use Jungle\Di\DiInterface;
	use Jungle\Di\DiSettingInterface;
	use Jungle\Di\HolderChains;
	use Jungle\Di\Injectable;

	/**
	 * Class Application
	 * @package Jungle
	 */
	abstract class Application extends Injectable{

		/** @var  bool */
		protected $initialized;

		/** @var  Loader */
		protected $loader;

		/** @var  Dispatcher */
		protected $dispatcher;

		/** @var bool */
		protected $cacheable = true;

		/**
		 * Application constructor.
		 * @param Loader $loader
		 */
		public function __construct(Loader $loader){
			$this->loader = $loader;

			$holderChains = new HolderChains();
			Di::setDefault($holderChains);
			$holderChains->insertHolder('default',($di = new Di()), 0);
			$this->setDi($holderChains);

			$di = $holderChains->getInjection('default');

			$this->initializeDirectories();

			$this->registerAutoload($loader);
			$di->setShared('loader',$loader);
			$di->setShared('application',$this);
			$di->setShared('view',  ($view = $this->initializeView($di)) );
			$this->dispatcher = $this->initializeDispatcher($view);
			$this->dispatcher->setDi($di);
			$di->setShared('dispatcher', $this->dispatcher );
			$this->registerDatabases($di->container('database')->useSelfOverlapping(true));
			$this->registerServices($di);

		}

		/**
		 * @return DiInterface|null
		 */
		public function getDefaultDi(){
			return $this->_dependency_injection->getInjection('default');
		}

		/**
		 * @param RequestInterface $request
		 * @return ResponseInterface
		 * @throws Dispatcher\Exception
		 * @throws \Jungle\Application\Dispatcher\Exception\NotCertainBehaviour
		 */
		public function handle(RequestInterface $request){
			return $this->dispatcher->dispatch($request);
		}

		/**
		 * @param ResponseInterface $response
		 * @return ResponseInterface
		 */
		protected function handleResponse(ResponseInterface $response){
			return $response;
		}

		/**
		 * @return Loader
		 */
		public function getLoader(){
			return $this->loader;
		}


		/**
		 * Initialize and check directories for current application
		 */
		protected function initializeDirectories(){
			$directories = [
				$this->getCacheDirname(),
				$this->getLogDirname(),
			];
			foreach($directories as $path){
				if(!is_dir($path)){
					mkdir($path,0555, true);
				}
			}
		}

		/**
		 * @param DiInterface|DiSettingInterface $di
		 * @return mixed
		 */
		abstract protected function registerDatabases(DiSettingInterface $di);

		/**
		 * @param DiInterface|DiSettingInterface $di
		 * @return mixed
		 */
		abstract protected function registerServices(DiSettingInterface $di);



		/**
		 * @param Loader $loader
		 */
		abstract protected function registerAutoload(Loader $loader);

		/**
		 * @return Dispatcher
		 */
		protected function createDispatcher(){
			return new Dispatcher();
		}

		/**
		 * @param ViewInterface $view
		 * @return Dispatcher
		 */
		protected function initializeDispatcher(ViewInterface $view){
			$dispatcher = $this->createDispatcher();
			$this->initializeDispatcherSettings($dispatcher);
			$this->initializeDispatcherModules($dispatcher);
			$this->initializeDispatcherStrategies($dispatcher, $view); // TODO Нужны только чекеры, а они у нас статичные в классе, поэтому как и с модулями
			return $dispatcher;
		}

		/**
		 * @param Dispatcher $dispatcher
		 */
		abstract protected function initializeDispatcherSettings(Dispatcher $dispatcher);

		/**
		 * @param Dispatcher $dispatcher
		 */
		abstract protected function initializeDispatcherModules(Dispatcher $dispatcher);

		/**
		 * @param Dispatcher $dispatcher
		 * @param ViewInterface $view
		 */
		abstract protected function initializeDispatcherStrategies(Dispatcher $dispatcher, ViewInterface $view);

		/**
		 * @param ViewInterface $view
		 */
		abstract protected function initializeViewRenderers(ViewInterface $view);

		/**
		 * @param $di
		 * @return ViewInterface
		 */
		protected function initializeView($di){
			$view = $this->createView();
			$view->setDi($di);
			$view->setBaseDirname($this->getViewsDirname());
			$view->setCacheDirname($this->getCacheDirname() . DIRECTORY_SEPARATOR . 'views');
			$this->initializeViewRenderers($view);
			return $view;
		}

		/**
		 * @return View
		 */
		protected function createView(){
			return new View();
		}

		/**
		 *
		 */
		public function refresh(){
			FileSystem::removeContain($this->getCacheDirname());
		}

		/**
		 * @return string
		 */
		abstract public function getViewsDirname();

		/**
		 * @return string
		 */
		abstract public function getModulesDirname();

		/**
		 * @return string
		 */
		abstract public function getModelsDirname();

		/**
		 * @return string
		 */
		abstract public function getCacheDirname();

		/**
		 * @return string
		 */
		abstract public function getLogDirname();

		/**
		 * @return string
		 */
		abstract public function getBaseDirname();


	}
}

