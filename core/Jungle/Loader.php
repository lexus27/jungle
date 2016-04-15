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

		protected $foundPath = null;


		protected $checkedPath = null;


		protected $prefixes = null;


		protected $classes = null;


		protected $extensions;


		protected $namespaces = null;


		protected $directories = null;


		protected $registered = false;


		/**
		 * @param string[] $extensions
		 * @return $this
		 */
		public function setExtensions(array $extensions){
			$this->extensions = $extensions;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getExtensions(){
			return $this->extensions;
		}

		/**
		 * @param array $dirs
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerDirs(array $dirs, $merge = false){
			$this->directories = $merge? array_merge((array)$this->directories,$dirs): $dirs;
			return $this;
		}

		/**
		 * @param array $namespaces
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerNamespaces(array $namespaces, $merge = false){
			$this->namespaces = $merge? array_merge((array)$this->namespaces,$namespaces): $namespaces;
			return $this;
		}

		/**
		 * @param array $classes
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerClasses(array $classes, $merge = false){
			$this->classes = $merge? array_merge((array)$this->classes,$classes): $classes;
			return $this;
		}

		/**
		 * @param array $prefixes
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerPrefixes(array $prefixes, $merge = false){
			$this->prefixes = $merge? array_merge((array)$this->prefixes,$prefixes): $prefixes;
			return $this;
		}


		/**
		 * @param $className
		 * @return bool
		 */
		public function autoLoad($className){

			if(strpos($className,'\\')!==false){

				if($this->classes !==null && isset($this->classes[$className]) && $this->classes[$className]){
					$path = $this->classes[$className];
					if(file_exists($path)){
						require $path;
						if(class_exists($className,false)){
							return true;
						}
					}
				}

				if($this->namespaces){
					// ClassName = \Kido\MyComponent\Lideal\Module
					// Namespaces = ['Kido','Kido\MyComponent']
					foreach($this->namespaces as $namespace => $path){
						$nsLen = strlen($namespace);
						if(substr($className,0,$nsLen)===$namespace){
							$nsChunk = substr($className,-$nsLen);
							foreach($this->extensions as $ext){
								$p = $path . $nsChunk . '.' . $ext;
								if(file_exists($p)){
									require $p;
									if(class_exists($className,false)){
										return true;
									}
								}
							}
						}
					}
				}

			}else{

				if($this->prefixes){
					// ClassName = Example_Adapter_Some
					// Prefixes = ['Class_Name' => 'fsd/sdfs/sfsdf/']
					foreach($this->prefixes as $prefix => $path){
						$pLen = strlen($prefix);
						if(substr($className, 0, $pLen) === $prefix){
							$pChunk = substr($className, -$pLen);
							foreach($this->extensions as $ext){
								$p = $path . $pChunk . '.' . $ext;
								if(file_exists($p)){
									require $p;
									if(class_exists($className, false)){
										return true;
									}
								}
							}
						}
					}
				}

				if($this->directories){
					foreach($this->directories as $directory){
						foreach($this->extensions as $ext){
							$p = $directory . $className . '.' . $ext;
							if(file_exists($p)){
								require $p;
								if(class_exists($className, false)){
									return true;
								}
							}
						}
					}
				}

			}
			return false;
		}

	}
}

