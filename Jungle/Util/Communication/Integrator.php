<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 12:13
 */
namespace Jungle\Util\Communication {

	use Jungle\Util\Communication\Integrator\Integration;
	use Jungle\Util\Communication\Integrator\Method;

	/**
	 * Class Integrator
	 * @package Jungle\Util\Communication
	 */
	class Integrator{

		/** @var  Integration[]  */
		protected $integrations = [];

		/** @var  callable */
		protected $listeners = [];

		/**
		 * @param $integration_key
		 * @param $method_path
		 * @param Integration $integration
		 * @param Method $method
		 */
		public function onExecuted($integration_key, $method_path, Integration $integration, Method $method){
			foreach($this->listeners as list($target,$listener)){
				if($this->checkTarget($integration_key,$method_path, $target)){
					call_user_func($listener,true, $integration_key, $method_path, $integration, $method);
				}
			}
		}


		/**
		 * @param $integration_key
		 * @param $method_path
		 * @param Integration $integration
		 * @param Method $method
		 */
		public function onFail($integration_key, $method_path, Integration $integration, Method $method){
			foreach($this->listeners as list($target,$listener)){
				if($this->checkTarget($integration_key,$method_path, $target)){
					call_user_func($listener,false, $integration_key, $method_path,$integration, $method);
				}
			}
		}



		/**
		 * @param $integration
		 * @param $method
		 * @param array $target
		 * @return bool
		 */
		public function checkTarget($integration, $method, array $target){
			foreach($target as $p){
				if($p['integration'] === $integration){
					if($p['method']===null || $p['method'] === $method){
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * @param $target
		 * @param callable $listener
		 * @return $this
		 */
		public function addListener($target, callable $listener){
			if(is_string($target)){
				$target = explode('#',$target);
				$target = [[
					'integration'   => isset($target[0])?$target[0]:null,
					'method'        => isset($target[1])?$target[1]:null,
				]];
			}else{
				$_t = [];
				foreach($target as $t){
					$t = explode('#',$t);
					$_t[] = [
						'integration'   => isset($t[0])?$t[0]:null,
						'method'        => isset($t[1])?$t[1]:null,
					];
				}
				$target = $_t;unset($_t);
			}
			$this->listeners[] = [$target, $listener];
			return $this;
		}


	}
}

