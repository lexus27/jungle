<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.05.2016
 * Time: 2:08
 */
namespace Jungle\Application\Dispatcher\Module {
	
	use Jungle\Application\Dispatcher\Module;

	/**
	 * Class StaticModule
	 * @package Jungle\Application\Dispatcher\Module
	 */
	class StaticModule extends Module{

		/** @var \ReflectionObject  */
		protected $reflection;

		/** @var string  */
		protected $base_namespace; // App\Modules\{ModuleClassName}

		/** @var string */
		protected $base_dirname;

		/** @var string  */
		protected $module_dirname;

		/** @var  bool  */
		protected $class_based = true;

		/** @var  string  */
		protected $controller_folder = 'Controller'; // App\Modules\{ModuleName}\{ControllerFolder}

		/** @var  string  */
		protected $cache_folder = '_cache';

		/**
		 * StaticModule constructor.
		 */
		public function __construct(){
			$this->reflection = new \ReflectionObject($this);
			$this->base_namespace = $this->reflection->getNamespaceName();
			$this->base_dirname = dirname($this->reflection->getFileName());
			$this->module_dirname = $this->reflection->getFileName() . DIRECTORY_SEPARATOR . basename(get_called_class());
			$this->controller_namespace = get_called_class() .'\\'. $this->controller_folder;
		}

		/**
		 * @return string
		 */
		public function getClassPath(){
			return $this->reflection->getFileName();
		}

		/**
		 * @return string
		 */
		public function getBaseDirname(){
			return $this->base_dirname;
		}

		/**
		 * @return string
		 */
		public function getCacheDirname(){
			if(isset($this->cache_dirname)){
				return $this->cache_dirname;
			}
			return $this->base_dirname . DIRECTORY_SEPARATOR . $this->getName() . DIRECTORY_SEPARATOR . $this->cache_folder;
		}

	}
}

