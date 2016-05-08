<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 1:05
 */
namespace App {

	/**
	 * Class Application
	 * @package App
	 */
	class Application{

		/** @var  \ReflectionClass */
		protected $reflection;

		protected $initialized = false;

		public function initialize(){

		}

		public function __construct(){
			$this->reflection = new \ReflectionClass($this);
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

		public function getBootstrapModules(){
			$modules = [];
			foreach(glob(dirname($this->reflection->getFileName()) . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . '*') as $path){
				if(is_dir($path)){
					$modules[basename($path)] = $path.'.php';
				}
			}
			return $modules;
		}

		public function getBootstrapRoutes(){

		}

		/**
		 *
		 */
		public function handle(){
			if(!$this->initialized){
				$this->initialized = true;
				$this->registerNamespaces();
				$this->registerModules();
				$this->registerRoutes();
				$this->initialize();
			}


			//code

		}


		protected function registerNamespaces(){

		}

		protected function registerModules(){

		}

		protected function registerRoutes(){

		}

	}
}

