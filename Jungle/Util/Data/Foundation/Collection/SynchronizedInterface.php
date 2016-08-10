<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.06.2016
 * Time: 2:51
 */
namespace Jungle\Util\Data\Foundation\Collection {

	/**
	 * Interface SynchronizedInterface
	 * @package Jungle\Util\Data\Foundation\Collection
	 */
	interface SynchronizedInterface{

		/**
		 * @return bool
		 */
		public function synchronize();

		/**
		 * @param $autoSync
		 * @return $this
		 */
		public function setAutoSync($autoSync);

		/**
		 * @return bool
		 */
		public function isAutoSync();

	}
}

