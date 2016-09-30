<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:57
 */
namespace Jungle\Util\Data\Schema {

	/**
	 * Interface ImmutableSwitchingInterface
	 * @package Jungle\Util\Data\Record
	 */
	interface ImmutableSwitchingInterface{

		/**
		 * @param bool|true $immutable
		 * @param $anxiety
		 * @return $this
		 */
		public function setImmutable($immutable = true, $anxiety = false);

		/**
		 * @return bool
		 */
		public function isImmutable();

	}
}

