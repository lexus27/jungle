<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:42
 */
namespace Jungle\Application {

	use Jungle\Application\Dispatcher\Controller\FormatterInterface;
	use Jungle\Application\Dispatcher\Controller\ProcessInitiatorInterface;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\Dispatcher\Exception;
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Application\Dispatcher\Exception\NotFound;
	use Jungle\Application\Dispatcher\Module;
	use Jungle\Application\Dispatcher\ModuleInterface;
	use Jungle\Application\Dispatcher\Router;
	use Jungle\Application\Dispatcher\RouterInterface;

	/**
	 * Class Dispatcher
	 * @package Jungle\Application
	 */
	class Dispatcher{

		/** @var  RouterInterface[] */
		protected $routers = [];

		/** @var  ModuleInterface[]|array[]  */
		protected $modules = [];

		/** @var  FormatterInterface[]  */
		protected $formatters = [];

		/** @var  string */
		protected $default_module = 'main';

		/** @var  bool  */
		protected $dispatching = false;

		/** @var  RequestInterface */
		protected $dispatching_request;


		/**
		 * @param FormatterInterface $formatter
		 * @return $this
		 */
		public function addFormatter(FormatterInterface $formatter){
			$this->formatters[] = $formatter;
			return $this;
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
					$module = $this->loadModule($moduleName,$module);
					$this->modules[$moduleName] = $module;
					return $module;
				}
			}
			return null;
		}

		/**
		 * @param $moduleName
		 * @param array $definition
		 * @return mixed
		 * @throws Exception
		 */
		protected function loadModule($moduleName,array $definition){
			$definition = array_replace([
				'class' => Module\DynamicModule::class,
			],$definition);
			$className = $definition['class'];
			if(class_exists($className)){
				$module = new $className();
				if(!$module instanceof ModuleInterface){
					throw new Exception('Module instance is not '.ModuleInterface::class);
				}
				$module->fromArray($definition);
				$module->setName($moduleName);
				$module->initialize();
				return $module;
			}else{
				throw new Exception('Module load error: not found module class "'.$className.'"');
			}
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
		 * @param RouterInterface $router
		 * @return $this
		 */
		public function addRouter(RouterInterface $router){
			$this->routers[] = $router;
			return $this;
		}

		/**
		 * @param RouterInterface $router
		 * @return mixed
		 */
		public function searchRouter(RouterInterface $router){
			return array_search($router,$this->routers,true);
		}

		/**
		 * @param RouterInterface $router
		 * @return $this
		 */
		public function removeRouter(RouterInterface $router){
			$i = $this->searchRouter($router);
			if($i!==false){
				array_splice($this->routers,$i,1);
			}
			return $this;
		}




		/**
		 * @param RequestInterface $request
		 * @return mixed
		 * @throws Control
		 * @throws Exception
		 * @throws NotFound
		 */
		public function dispatch(RequestInterface $request){
			if($this->dispatching){
				throw new \LogicException('dispatch already run!');
			}
			$this->dispatching = true;
			$this->dispatching_request = $request;
			try{

				// Routing
				$router = $this->getDesiredRouter($request);
				if(!$router){
					throw new Exception('Not found desired Router by Request!');
				}
				$routing = $router->match($request);

				// Control
				if($routing->isUnknown()){
					throw new NotFound('Not found route by request!');
				}else{
					$result = $this->control($routing->getReference(), $routing->getParams(), true, $routing);
				}

				// Prepare preferred response
				$response = $this->prepareResponse($result);

				$this->dispatching = false;
				$this->dispatching_request = null;

				return $response;

			}finally{
				$this->dispatching = false;
				$this->dispatching_request = null;
			}
		}


		/**
		 * @param $reference
		 * @param $data
		 * @param bool $format
		 * @param null|ProcessInitiatorInterface|ProcessInterface|Router\Routing $initiator
		 * @return mixed
		 * @throws Control
		 */
		public function control($reference,$data = null, $format = false, ProcessInitiatorInterface $initiator = null){
			$reference = self::normalizeReference($reference,[
				'module' => $this->default_module
			]);
			$moduleName = $reference['module'];
			$module = $this->getModule($moduleName);
			if(!$module){
				throw new Control('Module "'.$moduleName.'" not found!');
			}
			$process = $module->execute($this,(array) $data, $reference, $initiator);
			if($format){
				return $this->format($process,is_array($format)?$format:null);
			}else{
				return $process->getResult();
			}
		}

		/**
		 * @param ProcessInterface $process
		 * @param array $options
		 * @return mixed
		 */
		public function format(ProcessInterface $process,$options = null){
			if(is_array($options)){

			}elseif(is_string($options)){
				if($options === 'json'){
					return json_encode($process->getResult());
				}elseif($options === 'xml'){
					return json_encode($process->getResult());
				}
			}elseif($options = null){
				$options = $this->getFormatFromRequest($this->dispatching_request);
			}
			return $process->getResult();
		}

		public function getFormatFromRequest(RequestInterface $request){
			return [
				'type' => 'text/html'
			];
		}


		/**
		 * @param $result
		 * @return ResponseInterface
		 */
		protected function prepareResponse($result){

		}




		/**
		 * @param $process
		 * @return bool|void
		 */
		public function beforeControl(ProcessInterface $process){ }

		/**
		 * @param $process
		 * @param $result
		 */
		public function afterControl(ProcessInterface $process, $result){ }


		/**
		 * @param $reference
		 * @param array|null $default_reference
		 * @param bool $finallyNormalize
		 * @return array
		 */
		public static function normalizeReference($reference = null,array $default_reference = null, $finallyNormalize = true){
			if($reference === null) $reference = [];
			if(is_string($reference)){

				$module = null;
				$controller = null;
				$action = null;

				if(strpos($reference,':')!==false){
					$reference = explode(':',$reference);
					if(isset($reference[0])){
						if($reference[0]{0}==='#'){
							$module = substr($reference[0],1);
						}else{
							$controller = $reference[0];
						}
					}
					if(isset($reference[1])){
						if($controller!==null){
							$action = $reference[1];
						}else{
							$controller = $reference[1];
						}
					}
					if($action === null && isset($reference[2])){
						$action = $reference[2];
					}
				}else{
					$action = $reference;
				}

				if(strpos($action,'.')!==false){
					throw new \LogicException('Wrong string reference');
				}

				$reference = [
					'module' => $module,
					'controller' => $controller,
					'action' => $action
				];
				if($finallyNormalize){
					if($default_reference === null){
						$default_reference = [
								'module'		=> null,
								'controller'	=> null,
								'action'		=> null,
						];
					}
					foreach($default_reference as $k => $v){
						if(!isset($reference[$k])){
							$reference[$k] = $v;
						}
					}
				}
			}elseif($finallyNormalize){
				if($default_reference === null){
					$default_reference = [
						'module'		=> null,
						'controller'	=> null,
						'action'		=> null,
					];
				}
				foreach($default_reference as $k => $v){
					if(!isset($reference[$k])){
						$reference[$k] = $v;
					}
				}
			}
			if($finallyNormalize){
				foreach($reference as $k => $v){
					if(!is_null($v)){
						$reference[$k] = strtolower($v);
					}
				}
			}
			return $reference;
		}



		/**
		 * @param RequestInterface $request
		 * @return RouterInterface|null
		 */
		protected function getDesiredRouter(RequestInterface $request){
			foreach($this->routers as $router){
				if($router->isDesiredRequest($request))return $router;
			}
			return null;
		}

	}
}

