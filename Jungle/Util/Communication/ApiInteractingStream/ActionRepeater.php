<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.10.2016
 * Time: 22:33
 */
namespace Jungle\Util\Communication\ApiInteractingStream {
	
	use Jungle\Util\Communication\ApiInteracting\Combination as MainCombination;

	/**
	 * Class ActionRepeater
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	class ActionRepeater extends Action{


		protected $aggregator;


		/**
		 * ActionRepeater constructor.
		 * @param Api $api
		 * @param $definition
		 * @param callable $aggregator
		 * @param callable $validator
		 */
		public function __construct(Api $api, $definition, callable $aggregator, callable $validator = null){
			$this->aggregator = $aggregator;
			parent::__construct($api, $definition, $validator);
		}


		/**
		 * @param array $params
		 * @param MainCombination $combination
		 * @throws \Exception
		 */
		public function execute(array $params, MainCombination $combination){
			$this->combination = $combination;
			$aggregation = call_user_func($this->aggregator, $params, $this);
			if(!is_array($aggregation)){
				throw new \Exception('Aggregation is not valid returned value, must be each iteration params arrays');
			}
			if(!$aggregation){
				parent::execute($params, $combination);
			}else{
				foreach($aggregation as $item){
					parent::execute($item, $combination);
				}
			}
		}


	}
}

