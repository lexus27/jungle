<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:38
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Class Sequence
	 * @package Jungle\Util\Communication\Labaratory
	 */
	class Sequence implements SequenceInterface{

		use SpecificationAwareTrait;

		/**
		 * @param $action
		 * @param array $params
		 * @return ProcessInterface
		 */
		public function call($action, array $params){
			$sp = new SequenceProcess($this);
			$action = $this->getSpecification()->getAction($action);
			$action->execute($sp, $params);
			return $sp->getLastProcess();
		}

		/**
		 * @param array $definition
		 * @return SequenceProcessInterface
		 */
		public function sequence(array $definition){
			$sp = new SequenceProcess($this);
			try{
				$this->specification->beforeSequence($sp);
				foreach($definition as $item){
					$item = array_replace([
						'name'      => null,
						'params'    => null,
					],$item);
					$action = $this->specification->getAction($item['name']);
					$action->execute($sp,$item['params']);
				}
				$this->specification->afterSequence($sp);
			}finally{
				$this->specification->continueSequence($sp);
			}
			return $sp;
		}

	}
}

