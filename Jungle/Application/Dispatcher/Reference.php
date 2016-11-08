<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 14:08
 */
namespace Jungle\Application\Dispatcher {

	/**
	 * Interface Reference
	 * @package Jungle\Application\Dispatcher
	 *
	 *
	 * Приоритетные ссылки:
	 *
	 * "Эффект пузыря" - для вызова контроллера, это значит , что если нет указанного действия в текущем контроллере
	 * повторить попытку с контроллером - выше уровнем
	 *
	 * -    Для контроллера в пространстве имен, можно поднять уровень , убрав последнюю часть пути user.auth -> user
	 * -    Последним контроллером верхнего уровня в "Модуле" является "index"
	 *
	 * -    Понятие "модуль верхнего уровня", противоречиво, т.к модуль это отдельный субъект приложения,
	 *      как правило модули реализованны чтобы разделить приложения на части
	 *
	 * Calling tricks:
	 *
	 *      Action call: ...example call "error"...
	 *
	 *              Bubble Variant 1.1: (module safe priority, strict)
	 * -> { m: manager, c: user.auth,   a: error  }
	 * -> { m: manager, c: user,        a: error  }
	 * -> { m: manager, c: index,       a: error  }
	 *
	 *              Bubble Variant 1.2: (module safe priority, soft)
	 * -> { m: manager, c: user.auth,   a: error  }
	 * -> { m: manager, c: user,        a: error  }
	 * -> { m: manager, c: index,       a: error  }
	 * -> { m: index,   c: index,       a: error  } - with switching module
	 *
	 *              Bubble Variant 2.1: (controller safe priority, strict controller)
	 * -> { m: manager, c: user.auth,   a: error  }
	 * -> { m: index, c: user.auth,     a: error  }
	 *
	 *              Bubble Variant 2.2: (controller safe priority, strict by controller namespace)
	 * -> { m: manager, c: user.auth,   a: error  }
	 * -> { m: index, c: user.auth,     a: error  }
	 *
	 * -> { m: manager, c: user,        a: error  }
	 * -> { m: index, c: user,          a: error  }
	 *
	 *              Bubble Variant 2.3: (controller safe priority, soft)
	 * -> { m: manager, c: user.auth,   a: error  }
	 * -> { m: index, c: user.auth,     a: error  }
	 *
	 * -> { m: manager, c: user,        a: error  }
	 * -> { m: index, c: user,          a: error  }
	 *
	 * -> { m: manager, c: index,       a: error  }
	 * -> { m: index,   c: index,       a: error  }
	 *
	 *
	 * call: "product"
	 * -> { m: manager, c: user.auth,   a: product  }
	 * -> { m: manager, c: user,        a: product  }
	 * -> { m: manager, c: index,       a: product  }
	 *
	 * Параметр "Запрет переключения модуля",
	 * запрещает переключать модуль если нет указанных контроллеров по ссылке
	 * *:error
	 *
	 * -> { m: manager, c: user.auth,   a: index  }
	 * -> { m: index,   c: index,       a: product  }
	 *
	 * #manager:user.auth:error
	 * #manager:user:error
	 * #manager:index:error
	 * #index:index:error
	 *
	 * Default
	 * Bubble
	 * Current
	 *
	 *
	 *
	 * #module:controller:action
	 * &error -> #{current}:{current}:error
	 * &controller:action -> #{current}:controller:action
	 * #{default}:{current}:{current}
	 *
	 */
	abstract class Reference{


		const TYPE_BUBBLE    = 'bubble';
		const TYPE_CURRENT   = 'current';
		const TYPE_DEFAULT   = 'default';

		const SAFE_STRICT    = 2;
		const SAFE_NAMESPACE = 1;
		const SAFE_SOFT      = 0;


		/**
		 * @param               $reference
		 * @param array|null    $default_reference
		 * @param bool          $finallyNormalize
		 * @return array
		 */
		public static function normalize($reference = null, array $default_reference = null, $finallyNormalize = true){
			if($reference === null){
				$reference = [ ];
			}
			if(is_string($reference)){
				$module = null;
				$controller = null;
				$action = null;
				if(strpos($reference, ':') !== false){
					$reference = explode(':', $reference);
					if(isset($reference[0])){
						if($reference[0]{0} === '#'){
							$module = substr($reference[0], 1);
						}else{
							$controller = $reference[0];
						}
					}
					if(isset($reference[1])){
						if($controller !== null){
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

				if(strpos($action, '.') !== false){
					throw new \LogicException('Wrong string reference');
				}

				$reference = [
					'module'     => $module,
					'controller' => $controller,
					'action'     => $action
				];
			}
			if($finallyNormalize){
				if($default_reference === null){
					$default_reference = [
						'module'     => null,
						'controller' => null,
						'action'     => null,
					];
				}
				foreach($default_reference as $k => $v){
					if(!isset($reference[$k])){
						$reference[$k] = $v;
					}
				}
			}
			return $reference;
		}

		/**
		 * @param array $reference
		 * @param callable $replacer
		 * @return array
		 */
		public static function replace(array $reference, callable $replacer){
			foreach($reference as $k => & $v){
				$v = call_user_func($replacer, $k, $v);
			}
			return $reference;
		}

		/**
		 * @param array $reference
		 * @return string
		 */
		public static function stringify(array $reference){
			return "#{$reference['module']}:{$reference['controller']}:{$reference['action']}";
		}


		/**
		 * @param $reference
		 * @param array $priority
		 * @param bool $safeBy
		 * @param array|null $queue
		 * @param bool $queue_priority_order
		 * @return array
		 */
		public static function getSequence($reference, array $priority, $safeBy = null, array $queue = null, $queue_priority_order = true){
			$priority = array_replace([
				'module'        => self::SAFE_SOFT,
				'controller'    => self::SAFE_SOFT,
				'action'        => self::SAFE_SOFT
			],$priority);
			$references = [];
			if($queue===null){
				$queue = [ 'action', 'controller', 'module' ];
			}
			if($queue_priority_order) self::orderQueue($queue, $priority);

			if($safeBy){
				if(($i = array_search($safeBy,$queue, true)) !== false ) array_splice($queue,$i,1);
				$combinations = [];
				foreach($queue as $i => $name){
					$combinations[$name] = self::combineLink($reference[$name], 'index', $priority[$name]);
				}
				foreach(self::combineLink($reference[$safeBy], 'index', $priority[$safeBy]) as $safeLink){
					$reference[$safeBy] = $safeLink;
					foreach($queue as $i => $name){
						$combination = $combinations[$name];
						if($i > 0){
							array_shift($combination);
						}
						foreach($combination as $link){
							$reference[$name] = $link;
							$references[] = $reference;
						}
					}
				}
			}else{
				foreach($queue as $i => $name){
					$combination = self::combineLink($reference[$name],'index', $priority[$name]);
					if($i > 0){
						array_shift($combination);
					}
					foreach($combination as $link){
						$reference[$name] = $link;
						$references[] = $reference;
					}
				}
			}
			return $references;
		}

		/**
		 * @param $reference
		 * @param array $priority
		 * @param null $safeBy
		 * @param array|null $queue
		 * @param bool|true $queue_priority_order
		 * @return \Generator
		 */
		public static function generateSequence($reference, array $priority, $safeBy = null, array $queue = null, $queue_priority_order = true){
			$priority = array_replace([
				'module'        => self::SAFE_SOFT,
				'controller'    => self::SAFE_SOFT,
				'action'        => self::SAFE_SOFT
			],$priority);
			if($queue===null){
				$queue = [ 'action', 'controller', 'module' ];
			}
			if($queue_priority_order) self::orderQueue($queue, $priority);

			if($safeBy){
				if(($i = array_search($safeBy,$queue, true)) !== false ) array_splice($queue,$i,1);
				$combinations = [];
				foreach($queue as $i => $name){
					$combinations[$name] = self::combineLink($reference[$name], 'index', $priority[$name]);
				}
				foreach(self::combineLink($reference[$safeBy], 'index', $priority[$safeBy]) as $safeLink){
					$reference[$safeBy] = $safeLink;
					foreach($queue as $i => $name){
						$combination = $combinations[$name];
						if($i > 0){
							array_shift($combination);
						}
						foreach($combination as $link){
							$reference[$name] = $link;
							yield $reference;
						}
					}
				}
			}else{
				foreach($queue as $i => $name){
					$combination = self::combineLink($reference[$name],'index', $priority[$name]);
					if($i > 0){
						array_shift($combination);
					}
					foreach($combination as $link){
						$reference[$name] = $link;
						yield $reference;
					}
				}
			}
		}

		/**
		 * @param array $queue
		 * @param $config
		 */
		protected static function orderQueue(array & $queue, $config){
			$defaultQueue = $queue;
			usort($queue, function($a,$b) use($defaultQueue, $config) {
				if($config[$a]===$config[$b]){
					$a = array_search($a,$defaultQueue);
					$b = array_search($b,$defaultQueue);
					if($a === $b){
						return 0;
					}
					return $a>$b?1:-1;
				}
				return $config[$a]> $config[$b]?1:-1;
			});
		}

		/**
		 * @param $link
		 * @param $default
		 * @param $safeMode
		 * @return array
		 */
		protected static function combineLink($link, $default = 'index', $safeMode = self::SAFE_SOFT){
			$a = [];
			if($safeMode >= self::SAFE_STRICT){
				$a[] = $link;
			}else{
				if($safeMode <= self::SAFE_NAMESPACE){
					if(strpos($link,'.')!==false){
						$s = explode('.',$link);
						while($s){
							$a[] = implode('.',$s);
							array_pop($s);
						}
					}else{
						$a[] = $link;
					}
				}
				if($safeMode <= self::SAFE_SOFT){
					if($link !== $default){
						$a[] = $default;
					}
				}

			}
			return $a;
		}


		protected $module;

		protected $controller;

		protected $action;

		/**
		 * @return mixed
		 */
		public function getModule(){
			return $this->module;
		}

		public function getController(){
			return $this->controller;
		}

		public function getAction(){
			return $this->action;
		}

	}
}

