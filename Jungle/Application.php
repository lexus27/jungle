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
	use Jungle\Application\StrategyInterface;
	use Jungle\Application\View;
	use Jungle\Application\View\ViewStrategy;
	use Jungle\Application\ViewInterface;
	use Jungle\Di\DiInterface;
	use Jungle\Di\Injectable;
	use Jungle\Util\CacheableInterface;

	/**
	 * Class Application
	 * @package Jungle
	 */
	abstract class Application extends Injectable implements CacheableInterface{

		/** @var  bool */
		protected $initialized;

		/** @var  Loader */
		protected $loader;

		/** @var bool */
		protected $cacheable = true;

		/**
		 * Application constructor.
		 * @param Loader $loader
		 */
		public function __construct(Loader $loader){
			$this->loader = $loader;
			$di = $this->initializeDependencyInjector();
			$this->setDi($di);
			$this->initialize();
		}

		/**
		 * @param RequestInterface $request
		 * @return ResponseInterface
		 * @throws Dispatcher\Exception
		 * @throws \Jungle\Application\Dispatcher\Exception\NotCertainBehaviour
		 */
		public function handle(RequestInterface $request){
			$this->initialize();
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
		 * Main initialize method lazy with $this->initialized checking
		 */
		protected function initialize(){
			if(!$this->initialized){
				$this->initialized = true;
				$this->setup($this->_dependency_injector);
			}
		}

		/**
		 * @return DiInterface
		 */
		protected function initializeDependencyInjector(){
			return new Di();
		}

		/**
		 * @param DiInterface $di
		 */
		protected function setup(DiInterface $di){
			$this->initializeDirectories();
			$loader = $this->getLoader();
			$this->registerAutoload($loader);
			$di->setShared('loader',$loader);
			$di->setShared('application',$this);
			$view = $this->initializeView();
			$di->setShared('view',          $view);
			$di->setShared('dispatcher',    $this->initializeDispatcher($view));
			$this->registerDatabases($di->container('database')->useSelfOverlapping(true));
			$this->registerServices($di);
		}

		/**
		 * @param DiInterface $di
		 * @return mixed
		 */
		abstract protected function registerDatabases(DiInterface $di);

		/**
		 * @param DiInterface $di
		 * @return mixed
		 */
		abstract protected function registerServices(DiInterface $di);

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
		protected function initializeDispatcherSettings(Dispatcher $dispatcher){}

		/**
		 * @param Dispatcher $dispatcher
		 */
		abstract protected function initializeDispatcherModules(Dispatcher $dispatcher);

		/**
		 * Define in descendants methods on naming by pattern
		 * @method: createStrategy{Http}()
		 * - main router initializator
		 * @method: configureStrategy{Http}Services(StrategyInterface $strategy)
		 * - optional:
		 * @method: configureStrategy{Http}Router(StrategyInterface $strategy)
		 * - optional:
		 * @method: configureStrategy{Http}ViewStrategy(StrategyInterface $strategy)
		 * - optional:
		 * You can skip the router If initializer initializing the routes involved himself
		 *
		 * @param array $strategies
		 * @return array
		 */
		protected function getInitializeStrategies(array $strategies = [ ]){
			$strategies[] = 'http';
			return $strategies;
		}

		/**
		 * @return Http
		 */
		protected function createHttpStrategy(){
			return new Http();
		}

		/**
		 * @return Http\Router
		 */
		protected function initializeHttpRouter(){
			return new Http\Router();
		}

		/**
		 * @param Http\Router $router
		 */
		protected function initializeHttpRoutes(Http\Router $router){}

		/**
		 * @param StrategyInterface $strategy
		 * @param ViewInterface $view
		 * @return ViewStrategy
		 */
		protected function initializeHttpViewStrategy(StrategyInterface $strategy, ViewInterface $view){
			return new ViewStrategy();
		}

		/**
		 * @return ViewStrategy\RendererRule
		 */
		protected function initializeHttpHtmlRendererRule(){
			$rule = new ViewStrategy\RendererRule\HttpAcceptRendererRule('html');
			return $rule;
		}

		/**
		 * @return ViewStrategy\RendererRule\HttpAcceptRendererRule
		 */
		protected function initializeHttpJsonRendererRule(){
			$rule = new ViewStrategy\RendererRule\HttpAcceptRendererRule('json');
			return $rule;
		}


		/**
		 * @param ViewInterface $view
		 */
		abstract protected function initializeViewRenderers(ViewInterface $view);


		/**
		 * @param Dispatcher $dispatcher
		 * @param ViewInterface $view
		 */
		protected function initializeDispatcherStrategies(Dispatcher $dispatcher, ViewInterface $view){
			foreach($this->getInitializeStrategies() as $strategyAlias){
				/** @var StrategyInterface $strategy */
				$mName = 'create'.$strategyAlias.'Strategy';
				if(method_exists($this,$mName)){
					$strategy = $this->{$mName}();
					if($strategy){
						$strategy->setParent($this->_dependency_injector);
						$this->configureDispatcherStrategy($strategyAlias, $strategy, $view);
						$dispatcher->setStrategy($strategyAlias, $strategy);
					}
				}
			}
		}

		/**
		 * @param $strategyAlias
		 * @param StrategyInterface $strategy
		 * @param ViewInterface $view
		 */
		protected function configureDispatcherStrategy($strategyAlias,StrategyInterface $strategy, ViewInterface $view){
			$mName = 'initialize'.$strategyAlias.'Services';
			if(method_exists($this,$mName)){
				$this->{$mName}($strategy);
			}
			$mName = 'initialize'.$strategyAlias.'Router';
			if(method_exists($this,$mName)){
				$router = $this->{$mName}($strategy);
				if($router){
					$mName = 'initialize'.$strategyAlias.'Routes';
					if(method_exists($this,$mName)) $this->{$mName}($router);
					$strategy->setShared('router', $router);
				}
			}
			$mName = 'initialize'.$strategyAlias.'ViewStrategy';
			if(method_exists($this,$mName)){
				$view_strategy = $this->{$mName}($strategy, $view);
				if($view_strategy instanceof View\ViewStrategyInterface){
					foreach($view->getRendererAliases() as $name){
						$mName = 'initialize'.$strategyAlias.strtr($name,['.'=>'_','-'=>'_']).'RendererRule';
						if(method_exists($this,$mName)){
							$rule = $this->{$mName}($strategy, $view);
							if($rule){
								$view_strategy->addRule($name, $rule);
							}
						}
					}
					$strategy->setShared('view_strategy', $view_strategy);
				}
			}
		}

		/**
		 * @return View
		 */
		protected function createView(){
			return new View();
		}

		/**
		 * @return ViewInterface
		 */
		protected function initializeView(){
			$view = $this->createView();
			$view->setBaseDirname($this->getViewsDirname());
			$view->setCacheDirname($this->getCacheDirname() . DIRECTORY_SEPARATOR . 'views');
			$this->initializeViewRenderers($view);
			return $view;
		}

		/**
		 * @return Loader
		 */
		public function getLoader(){
			return $this->loader;
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

		/**
		 * @return $this
		 */
		public function cacheClear(){
			FileSystem::removeContain($this->getCacheDirname());
			return $this;
		}

		/**
		 * @return $this
		 */
		public function cacheOn(){
			$this->cacheable = true;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function cacheOff(){
			$this->cacheable = false;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function cacheIsEnabled(){
			return $this->cacheable;
		}


	}
}

