<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 23:38
 */
namespace Jungle\Util\Communication {

	use Jungle\Util\Communication\Sequence\CommandInterface;
	use Jungle\Util\Communication\Sequence\ProcessSequence;
	use Jungle\Util\Communication\Sequence\ProcessSequenceInterface;
	use Jungle\Util\Communication\Sequence\SpecificationInterface;

	/**
	 * Class SequenceTrait
	 * @package Jungle\Util\Communication
	 */
	trait SequenceTrait{

		/** @var  array */
		protected $sequence = [];

		/** @var  SpecificationInterface */
		protected $specification;

		/**
		 * @param array $sequence
		 * @throws Exception
		 */
		public function setSequence(array $sequence){
			foreach($sequence as & $item){
				$item = array_replace([
					'name'          => null,
					'params'        => null,
					'aggregation'   => null,
				],$item);
				if(!isset($item['aggregation']) || !isset($item['params'])){
					$item['params'] = (array)$item['params'];
				}
				$item['command'] = $this->specification->getCommand($item['name']);
				if(!$item['command']){
					throw new Exception('Not found command with name "'.$item['name'].'"');
				}

			}
			$this->sequence = $sequence;
		}

		/**
		 * @param SpecificationInterface $specification
		 * @return $this
		 */
		public function setSpecification(SpecificationInterface $specification){
			$this->specification = $specification;
			return $this;
		}

		/**
		 * @return SpecificationInterface
		 */
		public function getSpecification(){
			return $this->specification;
		}

		/**
		 * @return ProcessSequenceInterface
		 */
		public function run(){
			$processSequence = new ProcessSequence($this);
			foreach($this->sequence as $item){
				/** @var CommandInterface $command */
				$command = $item['command'];
				if(isset($item['aggregation'])){
					foreach($item['aggregation'] as $params){
						try{
							$command->run($processSequence, $params);
						}catch(Exception $exception){
							return $processSequence;
						}
					}
				}else{
					try{
						$command->run($processSequence, $item['params']);
					}catch(Exception $exception){
						return $processSequence;
					}
				}
			}
			return $processSequence;
		}

	}
}
