<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 19:26
 */
namespace Jungle\Util\Communication\Sequence {

	use Jungle\Util\Communication\Connection\StreamInteractionInterface;
	use Jungle\Util\Communication\Sequence;
	use Jungle\Util\Communication\SequenceInterface;

	/**
	 * Class Specification
	 * @package Jungle\Util\Communication\Sequence
	 */
	abstract class Specification implements SpecificationInterface{

		/** @var  CommandInterface[]  */
		protected $commands = [];

		/** @var  SequenceInterface[] */
		protected $sequences = [];

		/**
		 * @param $code
		 * @return bool
		 */
		public function isFatalCode($code){
			return $code >= 500;
		}

		/**
		 * @return Sequence|SequenceInterface
		 */
		public function createSequence(){
			$sequence = new Sequence();
			$sequence->setSpecification($this);
			return $sequence;
		}

		/**
		 * @param $name
		 * @param SequenceInterface $sequence
		 * @return $this
		 */
		public function setSequence($name, SequenceInterface $sequence){
			$this->sequences[$name] = $sequence;
			$sequence->setSpecification($this);
			return $this;
		}

		/**
		 * @param $name
		 * @return SequenceInterface|null
		 */
		public function getSequence($name){
			return isset($this->sequences[$name])?$this->sequences[$name]:null;
		}



		/**
		 * @param $name
		 * @param CommandInterface $command
		 * @return $this
		 */
		public function setCommand($name, CommandInterface $command){
			$this->commands[$name] = $command;
			$command->setSpecification($this);
			return $this;
		}

		/**
		 * @param $name
		 * @param array $properties
		 * @return $this
		 */
		public function command($name, array $properties){
			$this->setCommand($name, $this->_buildCommand($properties));
			return $this;
		}

		/**
		 * @param $name
		 * @param array $subCommands
		 * @return $this
		 */
		public function bundle($name, array $subCommands){
			foreach($subCommands as & $command){
				if(!$command instanceof CommandInterface){
					$command = $this->_buildCommand($command);
				}
			}
			$this->setCommand($name, new CommandBundle($subCommands));
			return $this;
		}

		/**
		 * @param $name
		 * @return CommandInterface|null
		 */
		public function getCommand($name){
			return isset($this->commands[$name])?$this->commands[$name]:null;
		}


		/**
		 * @param StreamInteractionInterface $connection $sequence
		 * @return mixed
		 */
		public function read(StreamInteractionInterface $connection){
			return $connection->read($this->getMaxLength());
		}

		/**
		 * @param StreamInteractionInterface $connection
		 * @param $data
		 * @return mixed
		 */
		public function send(StreamInteractionInterface $connection, $data){
			$data = $this->convertBeforeSend($data);
			return $connection->write($data);
		}





		/**
		 * @param array $properties
		 * @return Command
		 */
		protected function _buildCommand(array $properties){
			$properties = array_replace([
				'definition'    => null,
				'params'        => null,
				'aggregator'    => null,
				'rules'         => null
			],$properties);

			/**
			 * @var string|null $definition
			 * @var array|null $params
			 * @var array|null $rules
			 * @var callable|null $aggregator
			 */
			extract($properties);
			$command = new Command($definition);
			if(is_array($params)){
				$command->setParams($properties['definition']);
			}
			if(is_callable($aggregator)){
				$command->setAggregator($aggregator);
			}
			if(is_array($rules)){
				foreach($rules as $rule){
					if(is_array($rule)){
						$rule = new Rule($rule);
					}
					$command->addRule($rule);
				}
			}
			return $command;
		}

	}
}

