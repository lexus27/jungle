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
	
	use Jungle\Application\Adaptee\Cli\Dispatcher\Router as CLI_Router;
	use Jungle\Application\Adaptee\Http\Dispatcher\Router as HTTP_Router;
	use Jungle\Application\Dispatcher;
	use Jungle\Application\RequestInterface;
	use Jungle\Application\ResponseInterface;
	use Jungle\Application\View;
	use Jungle\Application\ViewInterface;
	use Jungle\Di\DiInterface;
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

		protected $default_renderer = 'html';

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
		 * @throws Dispatcher\Router\Exception\NotFound
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
			$di->setShared('dispatcher',    $this->initializeDispatcher());
			$di->setShared('view',          $this->initializeView());
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
		protected function registerServices(DiInterface $di){
			foreach($this->defineServices() as $alias => $definition){
				$di->setShared($alias, $definition);
			}
		}

		/**
		 * @param array $services
		 * @return array
		 */
		protected function defineServices(array $services = []){
			return $services;
		}

		/**
		 * Initialize and check directories for current application
		 */
		protected function initializeDirectories(){
			$directories = [
				$this->getCacheDirname()
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
		protected function initializeDispatcher(){
			$dispatcher = new Dispatcher();
			$this->initializeDispatcherSetting($dispatcher);
			$this->initializeDispatcherModules($dispatcher);
			$this->initializeDispatcherRouters($dispatcher);
			return $dispatcher;
		}

		/**
		 * @param Dispatcher $dispatcher
		 */
		protected function initializeDispatcherSetting(Dispatcher $dispatcher){}

		/**
		 * @param Dispatcher $dispatcher
		 */
		abstract protected function initializeDispatcherModules(Dispatcher $dispatcher);

		/**
		 * Define in descendants methods on naming by pattern
		 * @method: initialize{Http}Router()                            - main router initializator
		 * @method: initialize{Http}Routes(RouterInterface $router)     - optional:
		 * You can skip the router If initializer initializing the routes involved himself
		 *
		 * @param array $routers
		 * @return array
		 */
		protected function getRoutersDefinition(array $routers = []){
			$routers['http'] = HTTP_Router::class;
			return $routers;
		}

		/**
		 * @param Dispatcher $dispatcher
		 */
		protected function initializeDispatcherRouters(Dispatcher $dispatcher){
			foreach($this->getRoutersDefinition() as $alias => $className){
				$factoryMethod = 'initialize'.ucfirst($alias).'Router';
				if(method_exists($this,$factoryMethod)){
					$routesMethod = 'initialize'.ucfirst($alias).'Routes';
					$router = $this->{$factoryMethod}();
					if(method_exists($this,$routesMethod)){
						$this->{$routesMethod}($router);
					}
					$dispatcher->addRouter($alias,$router);
				}
			}
		}

		/**
		 * 
		 */
		protected function initializeCliRouter(){
			return new CLI_Router();
		}

		/**
		 * @param CLI_Router $cli
		 */
		protected function initializeCliRoutes(CLI_Router $cli){}
		
		/**
		 * @return HTTP_Router
		 */
		protected function initializeHttpRouter(){
			return new HTTP_Router();
		}
		
		/**
		 * @param HTTP_Router $router
		 */
		protected function initializeHttpRoutes(HTTP_Router $router){}
		
		/**
		 * @return ViewInterface
		 */
		protected function initializeView(){
			$matcher = new View\RendererMatcher($this->default_renderer);
			$view = new View($matcher);
			foreach($this->getViewRenderers() as $alias => $definition){
				$renderer = $definition['renderer'];
				$view->addRenderer($alias,$renderer);
				$matcher->addRule($alias,$definition['rule'],isset($definition['priority'])?$definition['priority']:0);
			}
			$this->initializeViewRenderers($view,$matcher);
			return $view;
		}

		/**
		 * @param array $renderers
		 * @return array
		 * ....
		 * 'renderer_alias' => [
		 *     'renderer' => RendererInterface,
		 *     'rule'     => callable(ProcessInterface $process, ViewInterface $view),
		 *     'priority' => int:default=0
		 * ]
		 * ...
		 */
		protected function getViewRenderers(array $renderers = []){
			$renderers['html'] = [
				'renderer' => new View\Renderer\Template\Twig(
					$this->getRendererDirname('html'),
					$this->getRendererCacheDirname('html','twig'),
					'twig'
				),
				'rule' => function(){
					return $this->request instanceof \Phalcon\Http\RequestInterface;
				},
				'priority' => 0
			];
			$renderers['json'] = [
				'renderer' => new View\Renderer\Data\Json(),
			    'rule' => function(){
				    return $this->request instanceof \Phalcon\Http\RequestInterface && $this->request->isAjax();
			    },
			    'priority' => 1000
			];
			return $renderers;
		}

		/**
		 * @param ViewInterface $view
		 * @param View\RendererMatcherInterface $matcher
		 */
		protected function initializeViewRenderers(ViewInterface $view, View\RendererMatcherInterface $matcher){}

		/**
		 * @param $rendererAlias
		 * @return string
		 */
		protected function getRendererDirname($rendererAlias){
			$dirname = $this->getViewsDirname() . DIRECTORY_SEPARATOR . $rendererAlias;
			if(!is_dir($dirname)){
				mkdir($dirname,0555,true);
			}
			return $dirname;
		}

		/**
		 * @param $rendererAlias
		 * @param string $type
		 * @return string
		 */
		protected function getRendererCacheDirname($rendererAlias, $type = 'unknown'){
			$dirname = $this->getCacheDirname() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $rendererAlias . DIRECTORY_SEPARATOR . $type;
			if(!is_dir($dirname)){
				mkdir($dirname,0555,true);
			}
			return $dirname;
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
		abstract public function getBaseDirname();

	}
}

