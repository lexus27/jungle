<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 24.02.2016
 * Time: 9:58
 */
namespace Jungle {

	/**
	 * Class Loader
	 * @package Jungle
	 */
	class Loader{

		protected static $default;

		/** @var  bool */
		protected $registered 	= false;

		/** @var  null|array */
		protected $checkedPath 	= [];

		/** @var  null|array */
		protected $prefixes 	= [];

		/** @var  null|array */
		protected $classes 		= [];

		/** @var  null|array */
		protected $extensions 	= ['php'];

		/** @var  null|array */
		protected $namespaces 	= [];

		/** @var  null|array */
		protected $directories 	= [];

		/** @var  mixed[]  */
		protected $scripts_execute_cache = [];

		protected $foundPaths = [];

		/**
		 * Idea for include file contains many classes in one namespace declared in single file
		 *
		 * @var array
		 */
		//protected $namespace_files = [];




		/**
		 * Loader constructor.
		 */
		public function __construct(){
			if(!self::$default){
				self::$default = $this;
			}
		}

		/**
		 * @return Loader
		 */
		public static function getDefault(){
			if(!self::$default){
				return new self();
			}
			return self::$default;
		}

		/**
		 * Загрузка скрипта с использованием кеширования в памяти
		 *
		 * Данный метод подразумевает загрузку структур данных из пхп файлов,
		 * для компонентов которые используют php файлы для хранения структуризованых данных,
		 * Защиты от выполнения потенциально опасного php кода здесь нет!
		 *
		 * @Example
		 * <?php return ['data','data','data','data','data','data','data','data',]; ?>
		 *
		 * @param $file_path
		 * @return mixed
		 */
		public function loadScriptData($file_path){
			if(!isset($this->scripts_execute_cache[$file_path])){
				if(is_readable($file_path) && is_file($file_path)){
					$this->scripts_execute_cache[$file_path] = require $file_path;
				}else{
					return false;
				}
			}
			return $this->scripts_execute_cache[$file_path];
		}

		/**
		 * @param string[] $extensions
		 * @param bool $merge
		 * @return $this
		 */
		public function setExtensions(array $extensions, $merge = false){
			$this->extensions = array_unique($merge? array_merge($this->extensions,$extensions): $extensions);
			return $this;
		}

		/**
		 * @return string[]
		 */
		public function getExtensions(){
			return $this->extensions;
		}

		/**
		 * @param string[] $dirs
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerDirs(array $dirs, $merge = false){
			foreach($dirs as & $path){
				$path = strtr($path,DIRECTORY_SEPARATOR === '\\'?'/':'\\',DIRECTORY_SEPARATOR);
			}
			$this->directories = $merge? array_replace($this->directories,$dirs): $dirs;
			return $this;
		}

		/**
		 * @param string[] $namespaces
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerNamespaces(array $namespaces, $merge = false){
			foreach($namespaces as & $path){
				$path = strtr($path,DIRECTORY_SEPARATOR === '\\'?'/':'\\',DIRECTORY_SEPARATOR);
			}
			$this->namespaces = $merge? array_replace($this->namespaces,$namespaces): $namespaces;
			return $this;
		}

		/**
		 * @param string[] $classes
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerClasses(array $classes, $merge = false){
			foreach($classes as & $path){
				$path = strtr($path,DIRECTORY_SEPARATOR === '\\'?'/':'\\',DIRECTORY_SEPARATOR);
			}
			$this->classes = $merge? array_replace($this->classes,$classes): $classes;
			return $this;
		}

		/**
		 * @param string[] $prefixes
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerPrefixes(array $prefixes, $merge = false){
			foreach($prefixes as & $path){
				$path = strtr($path,DIRECTORY_SEPARATOR === '\\'?'/':'\\',DIRECTORY_SEPARATOR);
			}
			$this->prefixes = $merge? array_replace($this->prefixes,$prefixes): $prefixes;
			return $this;
		}

		/**
		 * @param $namespace
		 * @return null
		 */
		public function getNamespacePath($namespace){
			if(isset($this->namespaces[$namespace])){
				return $this->namespaces[$namespace];
			}
			return null;
		}


		/**
		 * @param $namespace
		 * @return string
		 */
		public function getPathnameByNamespace($namespace){
			foreach($this->namespaces as $ns => $path){
				if(strpos($namespace, $ns)===0){
					$suffix = substr($namespace,strlen($ns));
					return $path . strtr($suffix,DIRECTORY_SEPARATOR === '\\'?'/':'\\',DIRECTORY_SEPARATOR);
				}
			}
			return null;
		}

		/**
		 * @param $pathname
		 * @return string
		 */
		public function getNamespaceByPathname($pathname){
			foreach($this->namespaces as $ns => $path){
				if(strpos($pathname,$path)===0){
					$suffix = substr($pathname,strlen($path));
					if(is_file($pathname)){
						$suffix = pathinfo($suffix,PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($suffix,PATHINFO_FILENAME);
					}
					return $ns . str_replace('/','\\',$suffix);
				}
			}
			return null;
		}

		/**
		 * @param $dirname
		 * @param null $namespace
		 * @param bool|int $depth
		 * @param array $container
		 * @return array
		 */
		public function scanClasses($dirname, $namespace = null, $depth = true, & $container = []){
			if(is_int($depth)){
				if(!$depth) return $container;
				$depth-=1;
			}
			foreach(glob($dirname . DIRECTORY_SEPARATOR . '*') as $path){
				if(is_dir($path)){
					if($depth){
						$namespaceName = ($namespace? $namespace . '\\':'') . basename($path);
						$this->scanClasses($path,$namespaceName, $depth, $container);
					}
				}else{
					$extension = pathinfo($path,PATHINFO_EXTENSION);
					if($this->isClassFileExtension($extension)){
						$className = ($namespace? $namespace . '\\':'') . pathinfo($path,PATHINFO_FILENAME);
						$container[$className] = $path;
					}
				}
			}
			return $container;
		}

		/**
		 * @param $extension
		 * @return bool
		 */
		public function isClassFileExtension($extension){
			foreach($this->extensions as $ext){
				if(strcasecmp($extension,$ext)===0){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param $class
		 * @return null
		 */
		public function getClassPath($class){
			if(isset($this->classes[$class])){
				return $this->classes[$class];
			}
			if(strpos($class,'\\',1)!==false){
				return $this->getPathnameByNamespace($class);
			}
			return null;
		}

		/**
		 * @param string $className - NameSpace\NameSpace\ClassClass or Vendor_Class or VendorClass
		 * @return bool
		 */
		public function autoLoad($className){
			if(strpos($className,'\\')!==false){/** Class In Namespace */

				if($this->namespaces){
					/**
					 *
					 * @Example
					 * @ClassName: 'Kido\MyComponent\Lideal\Module'
					 * @Namespaces: ['Kido\MyComponent' => 'folder/folder/folder/sub-folder']
					 *
					 * @Path: 'folder/folder/folder/sub-folder' . '\Lideal\Module' . '.php'
					 * @Problem: Если папки называются не учитывая регистр названия классов,
					 * могут быть проблемы с загрузкой файлов в регистрозависимых системах
					 */
					foreach($this->namespaces as $namespace => $path){
						$namespaceLength = strlen($namespace);
						if(substr($className,0,$namespaceLength)===$namespace){
							$namespace_class = substr($className,$namespaceLength);
							foreach($this->extensions as $ext){
								$p = $path . $namespace_class . '.' . $ext;
								if($this->check($p,$className)){
									return true;
								}
							}
						}
					}
				}

			}else{/** Class In Global Scope */

				if($this->prefixes){
					/**
					 * Prefixes = ['Class_Name_Some' => 'file/path/path/to/file']
					 * Prefixes = [{ClassPrefix!} => {FilePathForPrefix}]
					 * @Example
					 * @ClassName: 'VENDOR_HTTP_Adapter'
					 * @Prefixes: ['VENDOR_' => 'folder/folder/folder/sub-folder']
					 * @Path: 'folder/folder/folder/sub-folder/' . 'HTTP_Adapter' . '.php'
					 * @Problem: Регистрозависимость, если символ '_' подразумевает вход под-директорию (/),
					 * то символ не меняется на дир сепаратор
					 */
					foreach($this->prefixes as $prefix => $path_behind){
						$pLen = strlen($prefix);
						if(substr($className, 0, $pLen) === $prefix){
							$suffix = substr($className, $pLen);
							foreach($this->extensions as $ext){
								$p = $path_behind . DIRECTORY_SEPARATOR . $suffix . '.' . $ext;
								if($this->check($p,$className)){
									return true;
								}
							}
						}
					}
				}

				if($this->directories){
					/**
					 * @ClassName: VENDOR_HTTP_Adapter
					 * @Directories: ['folder/folder/folder/class_package_folder']
					 * @Path: 'folder/folder/folder/class_package_folder/' . 'VENDOR_HTTP_Adapter' . '.php'
					 *
					 *
					 * Сначала можно искать VENDOR_HTTP_Adapter,
					 * если такого нету то разбивать последовательно на под папки:
					 * 1 VENDOR{/}HTTP_Adapter,
					 * 2 VENDOR{/}HTTP{/}Adapter
					 * 3 VENDOR_HTTP{/}Adapter
					 * @Path: 'folder/folder/folder/class_package_folder/' . 'VENDOR/HTTP_Adapter' . '.php'
					 * @Path: 'folder/folder/folder/class_package_folder/' . 'VENDOR/HTTP/Adapter' . '.php'
					 * @Path: 'folder/folder/folder/class_package_folder/' . 'VENDOR_HTTP/Adapter' . '.php'
					 * И Это для одной только папки $directory!!!
					 */
					foreach($this->directories as $directory){
						foreach($this->extensions as $ext){
							$p = $directory . DIRECTORY_SEPARATOR . $className . '.' . $ext;
							if($this->check($p,$className)){
								return true;
							}
						}
					}
				}

			}

			if(isset($this->classes[$className]) && $this->classes[$className]){
				$path = $this->classes[$className];
				if($this->check($path,$className)){
					return true;
				}
			}

			return false;
		}

		protected function check($path,$className){
			if(!isset($this->foundPaths[$path])){
				if(file_exists($path)){
					$this->foundPaths[$path] = true;
					include $path;
					if(class_exists($className,false)){
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * @return bool
		 */
		public function register(){
			if(!$this->registered){
				if(!spl_autoload_register([$this,'autoLoad'])){
					return false;
				}else{
					$this->registered = true;
				}
			}
			return true;
		}

		/**
		 * @return bool
		 */
		public function unregister(){
			if($this->registered){
				if(!spl_autoload_unregister([$this,'autoLoad'])){
					return false;
				}else{
					$this->registered = false;
				}
			}
			return true;
		}


	}
}

