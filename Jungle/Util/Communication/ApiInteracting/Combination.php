<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 17:04
 */
namespace Jungle\Util\Communication\ApiInteracting {

	/**
	 * Class Combination
	 * @package Jungle\Util\Communication\ApiInteracting
	 */
	class Combination implements CombinatorInterface, \Iterator{

		/** @var  Api */
		protected $api;

		/** @var  Collector */
		protected $collector;

		/** @var array  */
		protected $sequence = [];

		/** @var array  */
		protected $actions_params = [];

		/** @var array  */
		protected $default_params = [];

		/** @var int  */
		protected $index = 0;

		/** @var bool  */
		protected $combined = false;

		/**
		 * @param array $action_order
		 * @return $this
		 */
		public function setList(array $action_order){
			foreach($action_order as $name){
				$this->sequence[] = $name;
				$this->actions_params[] = [];
			}
			return $this;
		}

		/**
		 * @param $action_name
		 * @param array $params
		 * @return $this
		 */
		public function push($action_name, array $params = []){
			$this->sequence[] = $action_name;
			$this->actions_params[] = $params;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getPassedParams(){
			$params = $this->actions_params[$this->index]?$this->actions_params[$this->index]:[];
			return array_replace($this->default_params, $params);
		}


		/**
		 * @param array $params
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setDefaultParams(array $params, $merge = false){
			$this->default_params = $merge?array_replace($this->default_params, $params):$params;
			return $this;
		}

		/**
		 * @return Collector
		 */
		public function getCollector(){
			return $this->collector;
		}

		/**
		 * @return Api
		 */
		public function getApi(){
			return $this->api;
		}

		/**
		 * @param Api $api
		 * @param Collector $collector
		 * @return Combination
		 */
		public function combine(Api $api, Collector $collector){
			$combination = clone $this;
			$combination->api = $api;
			$combination->collector = $collector;
			$combination->combined = true;
			return $combination;
		}


		/**
		 * @return ActionInterface
		 */
		public function current(){
			return $this->api->getAction($this->sequence[$this->index]);
		}

		public function next(){
			$this->index++;
		}

		/**
		 * @return string
		 */
		public function key(){
			return $this->sequence[$this->index];
		}

		/**
		 * @return bool
		 */
		public function valid(){
			return isset($this->sequence[$this->index]);
		}

		/**
		 *
		 */
		public function rewind(){
			$this->index = 0;
		}

		/**
		 * @param $action_name
		 * @param array $params
		 * @return $this
		 */
		public function setAfter($action_name, array $params = []){
			array_splice($this->sequence,$this->index+1,0,[$action_name]);
			array_splice($this->actions_params,$this->index+1,0, [$params]);
			return $this;
		}

		/**
		 * @param $next_action_name
		 * @param $action_name
		 * @param array $params
		 * @return $this
		 */
		public function insertBefore($next_action_name, $action_name, array $params = []){
			foreach($this->sequence as $i => $name){
				if($i > $this->index && $name === $next_action_name){
					array_splice($this->sequence,$i,0,[$action_name]);
					array_splice($this->actions_params,$i,0, [$params]);
				}
			}
			return $this;
		}

		/**
		 * @param $next_action_name
		 * @param $action_name
		 * @param array $params
		 * @return $this
		 */
		public function insertAfter($next_action_name, $action_name, array $params = []){
			foreach($this->sequence as $i => $name){
				if($i > $this->index && $name === $next_action_name){
					array_splice($this->sequence,$i+1,0,[$action_name]);
					array_splice($this->actions_params,$i+1,0, [$params]);
				}
			}
			return $this;
		}

		/**
		 * @param $action_name
		 * @return bool
		 */
		public function has($action_name){
			return in_array($action_name,$this->sequence, true);
		}

		/**
		 * @param $action_name
		 * @return $this
		 */
		public function dismiss($action_name){
			foreach(array_reverse($this->sequence,true) as $i => $name){
				if($i <= $this->index){
					break;
				}
				if($name === $action_name){
					array_splice($this->sequence,$i,1);
					array_splice($this->actions_params,$i,1);
				}
			}
			return $this;
		}

		/**
		 *
		 */
		public function complete(){

		}

	}
}

