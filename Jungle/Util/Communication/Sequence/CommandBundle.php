<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 23:01
 */
namespace Jungle\Util\Communication\Sequence {

	/**
	 * Class CommandBundle
	 * @package Jungle\Util\Communication\Sequence
	 */
	class CommandBundle implements CommandInterface{

		use CommandTrait;

		/** @var CommandInterface[] */
		protected $commands = [];


		/**
		 * Bundle constructor.
		 * @param null|CommandInterface[] $commands
		 */
		public function __construct(array $commands = null){
			if($commands){
				foreach($commands as $command){
					$this->addCommand($command);
				}
			}
		}

		/**
		 * @param SpecificationInterface $specification
		 * @return $this
		 */
		public function setSpecification(SpecificationInterface $specification){
			$this->specification = $specification;
			foreach($this->commands as $command){
				$command->setSpecification($specification);
			}
			return $this;
		}





		/**
		 * @param CommandInterface $command
		 * @return $this
		 */
		public function addCommand(CommandInterface $command){
			$this->commands[] = $command;
			$command->setSpecification($this->specification);
			return $this;
		}

		/**
		 * @param ProcessSequenceInterface $processSequence
		 * @param array $params
		 * @return void
		 */
		public function run(ProcessSequenceInterface $processSequence, array $params){
			foreach($this->commands as $command){
				$command->run($processSequence, $params);
			}
		}

	}
}

