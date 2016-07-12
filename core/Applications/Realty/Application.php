<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.07.2016
 * Time: 15:47
 */
namespace Applications\Realty {
	
	use Jungle\Application\RequestInterface;
	use Jungle\Http\Request;

	/**
	 * Class Application
	 * @package App
	 */
	class Application extends \Jungle\Application{

		/** @var  \ReflectionClass */
		protected $reflection;

		/** @var  bool  */
		protected $initialized = false;

		/** @var  string  */
		protected $modules_root_folder = 'Modules';

		/**
		 * Application constructor.
		 */
		public function __construct(){
			$this->reflection = new \ReflectionClass($this);
		}

		/**
		 *
		 */
		public function initialize(){

		}

		/**
		 * @return array
		 */
		public function getBootstrapNamespaces(){
			return [
				$this->reflection->getNamespaceName() => dirname($this->reflection->getFileName())
			];
		}

		/**
		 *
		 */
		public function getBootstrapRootDirectories(){
			return [
				'_cache'
			];
		}

		/**
		 * @return array
		 */
		public function getBootstrapModules(){
			$modules = [];
			foreach(glob(dirname($this->reflection->getFileName()) . DIRECTORY_SEPARATOR . $this->modules_root_folder . DIRECTORY_SEPARATOR . '*') as $path){
				if(is_dir($path)){
					$modules[basename($path)] = $path.'.php';
				}
			}
			return $modules;
		}

		/**
		 * @return array
		 */
		public function getBootstrapRoutes(){
			return [];
		}

		/**
		 * @param RequestInterface $request
		 * @return mixed
		 * @throws \Jungle\Application\Dispatcher\Exception
		 * @throws \Jungle\Application\Dispatcher\Router\Exception\NotFound
		 */
		public function handle(RequestInterface $request = null){
			if(!$this->initialized){
				$this->initialized = true;
				$this->registerNamespaces();
				$this->registerModules();
				$this->registerRoutes();
				$this->initialize();
			}
			if(!$request){
				$request = Request::getInstance();
			}
			return $this->dispatcher->dispatch($request);
		}

		/**
		 *
		 */
		protected function registerNamespaces(){
			$this->getLoader()->registerNamespaces($this->getBootstrapNamespaces());
		}

		/**
		 *
		 */
		protected function registerModules(){
			$this->getDispatcher()->registerModules($this->getBootstrapModules());
		}

		/**
		 *
		 */
		protected function registerRoutes(){

		}

		/**
		 *
		 */
		protected function registerDirectories(){
			$directories = $this->getBootstrapRootDirectories();
			foreach($directories as $path){
				if(!is_dir($path)){
					mkdir($path,0555,true);
				}
			}
		}


		/**
		 *
		 */
		public function getMemoryUsage(){

		}

		/**
		 *
		 */
		public function getDataBaseTotalSize(){

		}

		/**
		 *
		 */
		public function clearAllCache(){

		}

		/**
		 *
		 */
		public function getActionsCount(){

		}
	}
}

