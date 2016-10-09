<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:46
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Interface ProcessStackInterface
	 * @package Jungle\Util\Communication\Labaratory
	 */
	interface ProcessStackInterface{

		/**
		 * @param ProcessInterface $process
		 * @return $this
		 */
		public function addProcess(ProcessInterface $process);

		/**
		 * @return ProcessInterface|null
		 */
		public function getLastProcess();

	}
}

