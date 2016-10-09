<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 17:37
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Class SequenceProcess
	 * @package Jungle\Util\Communication\Labaratory
	 */
	class SequenceProcess implements SequenceProcessInterface, ProcessStackInterface{

		/** @var  SequenceInterface */
		protected $sequence;

		/** @var array  */
		protected $processes = [];

		/**
		 * SequenceProcess constructor.
		 * @param SequenceInterface $sequence
		 */
		public function __construct(SequenceInterface $sequence){
			$this->sequence = $sequence;
		}

		/**
		 * @return SequenceInterface
		 */
		public function getSequence(){
			return $this->sequence;
		}

		/**
		 * @return ProcessInterface
		 */
		public function getLastProcess(){
			return current(array_slice($this->processes,-1,1));
		}

		/**
		 * @param ProcessInterface $process
		 * @return $this
		 */
		public function addProcess(ProcessInterface $process){
			$this->processes[]=$process;
			return $this;
		}
	}
}

