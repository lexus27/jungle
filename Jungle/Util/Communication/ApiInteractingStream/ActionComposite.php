<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.10.2016
 * Time: 22:31
 */
namespace Jungle\Util\Communication\ApiInteractingStream {
	
	use Jungle\Util\Communication\ApiInteracting\Combination as MainCombination;

	/**
	 * Class ActionComposite
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	class ActionComposite extends Action{

		/** @var  Action[] */
		protected $actions = [];


		/**
		 * ActionComposite constructor.
		 * @param Api $api
		 * @param Action[] ...$actions
		 */
		public function __construct(Api $api, Action ...$actions){
			$this->actions = $actions;
			$this->api = $api;
		}

		/**
		 * @param Action $action
		 * @return $this
		 */
		public function addAction(Action $action){
			$this->actions[] = $action;
			return $this;
		}

		/**
		 * @param array $params
		 * @param MainCombination $combination
		 */
		public function execute(array $params, MainCombination $combination){
			foreach($this->actions as $action){
				$action->execute($params, $combination);
			}
		}

	}
}

