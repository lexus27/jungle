<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.10.2016
 * Time: 14:19
 */
namespace Jungle\EventManager {

	/**
	 * Class EventManager
	 * @package Jungle\EventManager
	 */
	class EventManager{

		/** @var  array  */
		protected $listeners = [];

		/** @var  Plugin[]  */
		protected $plugins = [];

		/**
		 * @param Plugin $plugin
		 * @return $this
		 */
		public function attachPlugin(Plugin $plugin){
			$this->plugins[] = $plugin;
			return $this;
		}

		/**
		 * @param $event_name
		 * @param callable $listener
		 * @param null $alias
		 * @throws \Exception
		 * @internal param $listener_alias
		 * @internal param bool $deferred
		 */
		public function attachListener($event_name, $listener, $alias = null){
			if(!is_object($listener) && !is_callable($listener)){
				throw new \Exception('Listener is invalid, not callable');
			}
			list($component, $sub) = array_replace([null,null],explode(':', $event_name, 2));
			$this->listeners[$component][] = [$sub, $alias, $listener,false,0];
		}

		/**
		 * @param $event_name
		 * @param $listener
		 * @param null $alias
		 * @throws \Exception
		 */
		public function deferListener($event_name, $listener, $alias = null){
			if(!is_object($listener) && !is_callable($listener)){
				throw new \Exception('Listener is invalid, not callable');
			}
			list($component, $sub) = array_replace([null,null],explode(':', $event_name, 2));
			$this->listeners[$component][] = [$sub, $alias, $listener,true,0];
		}

		/**
		 * @param $event_name
		 * @param $listener
		 * @param int $count
		 * @param null $alias
		 * @throws \Exception
		 */
		public function dryListener($event_name, $listener, $count = null, $alias = null){
			if(!is_object($listener) && !is_callable($listener)){
				throw new \Exception('Listener is invalid, not callable');
			}
			$count = $count===null?1:$count;
			list($component, $sub) = array_replace([null,null],explode(':', $event_name, 2));
			$this->listeners[$component][] = [$sub, $alias, $listener,$count,0];
		}

		/**
		 * @param $event_name
		 * @param $listener
		 */
		public function removeListener($event_name, $listener){
			list($component, $event_name) = array_replace([null,null],explode(':', $event_name, 2));
			$component = strtolower($component);
			if(isset($this->listeners[$component])){
				foreach($this->listeners[$component] as $i => $l){
					if($listener === $l[2]){
						unset($this->listeners[$component][$i]);
					}
				}
			}
		}

		/**
		 * @param $event_name
		 * @param $alias
		 */
		public function removeListenerByAlias($event_name, $alias){
			list($component, $event_name) = array_replace([null,null],explode(':', $event_name, 2));
			$component = strtolower($component);
			if(isset($this->listeners[$component])){
				foreach($this->listeners[$component] as $i => $l){
					if($alias === $l[1]){
						unset($this->listeners[$component][$i]);
					}
				}
			}
		}

		/**
		 * @param $event_name
		 * @param $arguments
		 * @return array|bool
		 */
		public function invokeEvent($event_name, ...$arguments){
			list($component, $invokedEventName) = array_replace([null,null],explode(':', $event_name, 2));
			$component = strtolower($component);
			usort($this->plugins,[$this,'_plugin_cmp']);
			foreach($this->plugins as $plugin){
				if($plugin->matchPreferredEvent($event_name)){
					if(method_exists($plugin, ($full_method = $component.'__'.$invokedEventName))){
						return call_user_func_array([$plugin,$full_method],$arguments);
					}
				}
			}
			if(isset($this->listeners[$component])){
				foreach($this->listeners[$component] as $i => $l){
					list($eventName, $alias, $listener, $deferred, $call_count) = $l;
					if($eventName === $invokedEventName){
						$call_count = & $l[4];
						$this->_call($component, $eventName, $invokedEventName, $listener, $arguments);
						$call_count++;
						if($deferred === true){
							$deferred = 1;
						}
						if($call_count >= $deferred){
							unset($this->listeners[$component][$i]);
						}
					}
				}
			}
			return true;
		}

		/**
		 * @param Plugin $a
		 * @param Plugin $b
		 * @return int
		 */
		protected function _plugin_cmp($a, $b){
			$a = $a->getPriority();
			$b = $b->getPriority();
			return $a==$b?0:($a>$b?1:-1);
		}

		/**
		 * @param $event_name
		 * @param ...$arguments
		 * @return array
		 */
		public function invokeCollector($event_name, ...$arguments){
			list($component, $invokedEventName) = array_replace([null,null],explode(':', $event_name, 2));
			$component = strtolower($component);
			$collection = [];
			usort($this->plugins,[$this,'_plugin_cmp']);
			foreach($this->plugins as $plugin){
				if($plugin->matchPreferredEvent($event_name)){
					if(method_exists($plugin, ($full_method = $component.'__'.$invokedEventName))){
						$collection[] = call_user_func_array([$plugin,$full_method],$arguments);
					}
				}
			}
			if(isset($this->listeners[$component])){
				foreach($this->listeners[$component] as $i => $l){
					list($eventName, $alias, $listener, $deferred, $call_count) = $l;
					$call_count = & $l[4];
					$result = $this->_call($component, $eventName, $invokedEventName, $listener, $arguments);
					if($result!==null){
						$collection[] = $result;
					}

					$call_count++;

					if($deferred === true){
						$deferred = 1;
					}

					if($call_count >=$deferred){
						unset($this->listeners[$component][$i]);
					}

				}
			}
			return $collection;
		}

		/**
		 * @param $event_name
		 * @param ...$arguments
		 * @return bool
		 */
		public function invokeStoppable($event_name, ...$arguments){
			list($component, $invokedEventName) = array_replace([null,null],explode(':', $event_name, 2));
			$component = strtolower($component);
			usort($this->plugins,[$this,'_plugin_cmp']);
			foreach($this->plugins as $plugin){
				if($plugin->matchPreferredEvent($event_name)){
					if(method_exists($plugin, ($full_method = $component.'__'.$invokedEventName))){
						$collection[] = call_user_func_array([$plugin,$full_method],$arguments);
					}
				}
			}
			if(isset($this->listeners[$component])){
				foreach($this->listeners[$component] as $i => $l){
					list($eventName, $alias, $listener, $deferred, $call_count) = $l;
					$call_count = & $l[4];
					$result = $this->_call($component, $eventName, $invokedEventName, $listener, $arguments);
					if($result===false){
						return false;
					}
					$call_count++;
					if($deferred === true){
						$deferred = 1;
					}
					if($call_count >=$deferred){
						unset($this->listeners[$component][$i]);
					}
				}
			}
			return true;
		}

		protected function _call($ctName, $eName, $invokedEName, $listener, array $arguments){
			if(is_callable($listener)){
				return call_user_func_array($listener, $arguments);
			}elseif(is_object($listener)){
				if(!$eName || $eName === $invokedEName){
					if(method_exists($listener, ($full_method = $ctName.'__'.$eName))){
						return call_user_func_array([$listener,$full_method],$arguments);
					}elseif(method_exists($listener, $invokedEName)){
						return call_user_func_array([$listener,$invokedEName],$arguments);
					}
				}
			}
			return null;
		}

	}
}

