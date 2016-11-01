<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.10.2016
 * Time: 10:46
 */
namespace Jungle\Application {

	use Jungle\Application\Dispatcher\Module\DynamicModule;
	use Jungle\Application\Dispatcher\ModuleInterface;

	/**
	 * Class MCASystem
	 * @package Jungle\Application
	 *
	 * Вынес логику отвечающую за хранение данных по MCA сюда. из Диспетчера
	 *
	 */
	class MCASystem{

		/** @var  ModuleInterface[]|array[]  */
		protected $modules = [];


		/** @var  string */
		protected $default_module       = 'index';

		/** @var  string */
		protected $default_controller   = 'index';

		/** @var  string */
		protected $default_action       = 'index';


		/** @var  string */
		protected $action_suffix        = 'Action';

		/** @var  string */
		protected $controller_suffix    = 'Controller';





		/**
		 * @param $name
		 * @return $this
		 */
		public function setDefaultModule($name){
			$this->default_module = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultModule(){
			return $this->default_module;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setDefaultController($name){
			$this->default_controller = $name;
			return $this;
		}
		/**
		 * @return string
		 */
		public function getDefaultController(){
			return $this->default_controller;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setDefaultAction($name){
			$this->default_action = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultAction(){
			return $this->default_action;
		}




		/**
		 * @param ModuleInterface $module
		 * @param bool $check
		 * @return $this
		 */
		public function addModule(ModuleInterface $module, $check = true){
			if($check){
				$name = $module->getName();
				foreach($this->modules as $index => $m){
					if($m->getName() === $name){
						throw new \LogicException('Module "'.$name.'" already exists!');
					}
				}
			}
			$this->modules[$module->getName()] = $module;
			return $this;
		}

		/**
		 * @param $moduleName
		 * @return null|ModuleInterface
		 */
		public function getModule($moduleName){
			if(isset($this->modules[$moduleName])){
				$module = $this->modules[$moduleName];
				if($module instanceof ModuleInterface){
					return $module;
				}else{
					$module = $this->_loadModule($moduleName,$module);
					$this->modules[$moduleName] = $module;
					return $module;
				}
			}
			return null;
		}

		/**
		 * @param array $modules key pairs assoc
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerModules(array $modules, $merge = false){
			foreach($modules as $moduleName => & $moduleDefinition){
				if($moduleDefinition instanceof ModuleInterface){
					$moduleDefinition->setName($moduleName);
				}elseif(is_string($moduleDefinition)){
					$moduleDefinition = [
						'class' => $moduleDefinition
					];
				}
			}
			$this->modules = $merge?array_replace($this->modules,$modules):$modules;
			return $this;
		}

		/**
		 * @param $module
		 * @return $this
		 */
		public function removeModule($module){
			if(isset($this->modules[$module])){
				unset($this->modules[$module]);
			}
			return $this;
		}




		/**
		 * @param $moduleName
		 * @param array $definition
		 * @return ModuleInterface
		 * @throws Exception
		 */
		protected function _loadModule($moduleName,array $definition){
			$definition = array_replace([
				'class' => DynamicModule::class,
			],$definition);
			$className = $definition['class'];
			if(class_exists($className)){
				$module = new $className();
				if(!$module instanceof ModuleInterface){
					throw new Exception('Module instance is not '.ModuleInterface::class);
				}
				$module->fromArray($definition);
				$module->setName($moduleName);
				$module->initialize($this);
				return $module;
			}else{
				throw new Exception('Module load error: "'.$moduleName.'" module, not found module class "'.$className.'"');
			}
		}

	}
}

