<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.09.2016
 * Time: 17:12
 */
namespace Jungle\Di {

	use Jungle\Exception;

	/**
	 * Class Chains
	 * @package Jungle\Di
	 */
	class Chains implements ChainsInterface{


		/** @var  DiInterface[]  */
		protected $dependency_injections = [];

		/** @var  array  */
		protected $holders_history = [];

		/** @var  array  */
		protected $holders = [];

		/** @var bool  */
		protected $holders_sorted = false;

		/**
		 * @param $alias
		 * @param $priority
		 * @return $this
		 */
		public function defineHolder($alias, $priority = 0.0){
			$this->holders[$alias] = floatval($priority);
			$this->holders_sorted = false;
			return $this;
		}

		/**
		 * @param $holderAlias
		 * @return $this
		 * @throws Exception
		 */
		public function restoreInjection($holderAlias){
			if(array_key_exists($holderAlias, $this->holders_history)){
				if(!empty($this->holders_history[$holderAlias])){
					$this->dependency_injections[$holderAlias] = array_pop($this->holders_history[$holderAlias]);
				}
			}else{
				throw new Exception('Holder with alias "'.$holderAlias.'" not defined');
			}
			return $this;
		}

		/**
		 * @param $holderAlias
		 * @param DiInterface $di
		 * @return $this
		 */
		public function changeInjection($holderAlias, DiInterface $di){
			if(array_key_exists($holderAlias, $this->dependency_injections)){
				if($this->dependency_injections[$holderAlias]){
					$previous = $this->dependency_injections[$holderAlias];
					$this->holders_history[$holderAlias][] = $previous;
				}
				$this->dependency_injections[$holderAlias] = $di;
			}else{
				//error
			}
			return $this;
		}

		/**
		 * @param $holderAlias
		 * @return mixed|void
		 * @throws Exception
		 */
		public function getLastInjection($holderAlias){
			if(array_key_exists($holderAlias, $this->holders_history)){
				end($this->holders_history[$holderAlias]);
				return current($this->holders_history[$holderAlias]);
			}else{
				throw new Exception('Holder with alias "'.$holderAlias.'" not defined');
			}
		}

		/**
		 * @param $holderAlias
		 * @return DiInterface|null
		 */
		public function getInjection($holderAlias){
			return isset($this->dependency_injections[$holderAlias])?$this->dependency_injections[$holderAlias]:null;
		}

		/**
		 *
		 */
		public function sortHolders(){
			$holders = array_keys($this->holders);
			usort($holders, function($a,$b){
				$priorityA = $this->holders[$a];
				$priorityB = $this->holders[$b];
				if($priorityA == $priorityB) return 0;
				return $priorityA > $priorityB?1:-1;
			});
			$_h = [];
			foreach($holders as $key){
				$_h[$key] = $this->holders[$key];
			}
			$this->holders = $_h;
			$this->holders_sorted = true;
		}

		/**
		 * @param $name
		 * @return ServiceInterface|null
		 */
		public function getService($name){
			if(!$this->holders_sorted){
				$this->sortHolders();
			}
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key]) || !$di->has($name)) continue;
				return $di->getService($name);
			}
			return null;
		}



	}
}

