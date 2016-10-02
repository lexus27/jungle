<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 20:55
 */
namespace Jungle\Util\Communication\Sequence {

	use Jungle\Util\Communication\Sequence;
	use Jungle\Util\Communication\SequenceInterface;

	/**
	 * Class ProcessSequence
	 * @package Jungle\Util\Communication\Sequence
	 */
	class ProcessSequence implements ProcessSequenceInterface{

		/** @var  SequenceInterface */
		protected $sequence;

		/** @var  array */
		protected $params = [];

		/** @var  ProcessInterface[]  */
		protected $processes = [];

		/** @var bool  */
		protected $success = true;

		/**
		 * ProcessSequence constructor.
		 * @param SequenceInterface $sequence
		 */
		public function __construct(SequenceInterface $sequence){
			$this->sequence = $sequence;
		}

		/**
		 * @param ProcessInterface $process
		 * @return $this
		 */
		public function addProcess(ProcessInterface $process){
			$this->processes[] = $process;
			if($process->isCanceled()){
				$this->success = false;
			}
			return $this;
		}


		/**
		 * @return array
		 */
		public function getParams(){
			return $this->params;
		}

		/**
		 * @return SequenceInterface
		 */
		public function getSequence(){
			return $this->sequence;
		}

		/**
		 * @return array
		 */
		public function isSuccess(){
			return $this->success;
		}

		/**
		 * @return ProcessInterface
		 */
		public function getLastProcess(){
			return current(array_slice($this->processes,-1,1));
		}

	}
}

