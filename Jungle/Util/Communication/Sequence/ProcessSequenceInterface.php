<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 20:30
 */
namespace Jungle\Util\Communication\Sequence {

	use Jungle\Util\Communication\Sequence;

	/**
	 * Interface ProcessSequenceInterface
	 * @package Jungle\Util\Communication\Sequence
	 */
	interface ProcessSequenceInterface{



		/**
		 * @param ProcessInterface $process
		 * @return $this
		 */
		public function addProcess(ProcessInterface $process);

		/**
		 * @return Sequence
		 */
		public function getSequence();

		/**
		 * @return array
		 */
		public function getParams();

		/**
		 * @return bool
		 */
		public function isSuccess();


		/**
		 * @return ProcessInterface
		 */
		public function getLastProcess();
	}
}

