<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:33
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Interface SequenceProcessInterface
	 * @package Jungle\Util\Communication\Labaratory
	 */
	interface SequenceProcessInterface{

		/**
		 * @return SequenceInterface
		 */
		public function getSequence();

		/**
		 * @return ProcessInterface
		 */
		public function getLastProcess();

	}
}

