<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 17:43
 */
namespace Jungle\Util\ProcessUnit {

	/**
	 * Class ProcessComposite
	 * @package Jungle\Util\ProcessUnit
	 */
	class ProcessComposite extends Process implements ProcessCollectorInterface{

		/** @var  ProcessCollectorInterface */
		protected $collector;
		
		/** @var  Process[]  */
		protected $processes = [];

		/**
		 * @param ProcessCollectorInterface $collector
		 * @return $this
		 */
		public function setCollector(ProcessCollectorInterface $collector){
			$this->collector = $collector;
			return $this;
		}

		/**
		 * @param ProcessInterface $process
		 * @return $this
		 */
		public function push(ProcessInterface $process){
			$this->processes[] = $process;
			return $this;
		}

		/**
		 * @return ProcessInterface|ProcessComposite
		 */
		public function last(){
			return end($this->processes);
		}

		/**
		 * @return ProcessInterface|ProcessComposite
		 */
		public function first(){
			reset($this->processes);
			return current($this->processes);
		}

		/**
		 * @return int
		 */
		public function count(){
			return count($this->processes);
		}

		/**
		 * @return ProcessInterface[]|ProcessComposite[]
		 */
		public function getCollection(){
			return $this->processes;
		}
	}
}

