<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.07.2016
 * Time: 2:14
 */
namespace Jungle\Application {
	
	use Jungle\Application;
	use Jungle\Loader;

	/**
	 * Class StaticApplication
	 * @package Jungle\Application
	 */
	abstract class StaticApplication extends Application{

		/** @var \ReflectionObject  */
		protected $reflection;

		/** @var  string  */
		protected $class_path;

		/** @var  string  */
		protected $modules_dirname;

		/** @var  string  */
		protected $views_dirname;

		/** @var  string  */
		protected $models_dirname;

		/** @var  string  */
		protected $cache_dirname;

		/** @var  string */
		protected $log_dirname;

		/** @var  string  */
		protected $base_dirname;

		/** @var  string  */
		protected $views_folder     = 'Views';

		/** @var  string  */
		protected $cache_folder     = '_cache';

		protected $log_folder       = '_log';

		/** @var  string  */
		protected $modules_folder   = 'Modules';

		/** @var  string  */
		protected $models_folder    = 'Models';




		/**
		 * StaticApplication constructor.
		 * @param Loader $loader
		 */
		public function __construct(Loader $loader){
			$this->reflection = new \ReflectionObject($this);
			$this->class_path = $classPath = $this->reflection->getFileName();
			$this->base_dirname = dirname($this->class_path);
			$prefix = $this->base_dirname . DIRECTORY_SEPARATOR;
			$this->views_dirname    = $prefix . $this->views_folder;
			$this->modules_dirname  = $prefix . $this->modules_folder;
			$this->cache_dirname    = $prefix . $this->cache_folder;
			$this->log_dirname      = $prefix . $this->log_folder;
			$this->models_dirname   = $prefix . $this->models_folder;
			parent::__construct($loader);
		}

		/**
		 * @param Dispatcher $dispatcher
		 * @return mixed
		 */
		protected function initializeDispatcherModules(Dispatcher $dispatcher){
			$modules = [];
			$namespace = $this->loader->getNamespaceByPathname($this->modules_dirname);
			foreach(glob($this->modules_dirname . DIRECTORY_SEPARATOR . '*') as $path){
				if(is_dir($path)){
					$basename = basename($path);
					$modules[strtolower($basename)] = $namespace . '\\' . $basename;
				}
			}
			$dispatcher->registerModules($modules);
		}


		/**
		 * @return string
		 */
		public function getClassPath(){
			return $this->class_path;
		}

		/**
		 * @return string
		 */
		public function getViewsDirname(){
			return $this->views_dirname;
		}

		/**
		 * @return string
		 */
		public function getModelsDirname(){
			return $this->models_dirname;
		}

		/**
		 * @return string
		 */
		public function getModulesDirname(){
			return $this->modules_dirname;
		}

		/**
		 * @return mixed
		 */
		public function getCacheDirname(){
			return $this->cache_dirname;
		}

		public function getLogDirname(){
			return $this->log_dirname;
		}

		/**
		 * @return mixed
		 */
		public function getBaseDirname(){
			return $this->base_dirname;
		}


	}
}

