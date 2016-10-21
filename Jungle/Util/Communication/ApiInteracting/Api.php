<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 16:31
 */
namespace Jungle\Util\Communication\ApiInteracting {

	/**
	 * Class Api
	 * @package Jungle\Util\Communication\ApiInteracting
	 */
	abstract class Api{

		/** @var  ActionInterface[]  */
		protected $actions = [];

		/**
		 * @return Collector
		 */
		public function createCollector(){
			return new Collector($this);
		}

		/**
		 * @param CombinatorInterface $combinator
		 * @return Collector
		 */
		public function interact(CombinatorInterface $combinator){
			$combination = $combinator->combine($this,$this->createCollector());
			$this->before($combination);
			foreach($combination as $action){
				$this->executeAction($action, $combination);
			}
			$this->after($combination);
			$combination->complete();
			return $combination->getCollector();
		}

		public function before(Combination $combination){}

		public function after(Combination $combination){}

		/**
		 * @param $name
		 * @return ActionInterface
		 */
		public function getAction($name){
			return $this->actions[$name];
		}

		/**
		 * @param $name
		 * @param ActionInterface $action
		 * @return $this
		 */
		public function setAction($name, ActionInterface $action){
			$this->actions[$name] = $action;
			return $this;
		}

		/**
		 * @param ActionInterface $action
		 * @param Combination $combination
		 */
		protected function executeAction(ActionInterface $action, Combination $combination){
			$action->execute($combination->getPassedParams(), $combination);
		}


	}
}

