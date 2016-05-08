<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 14:08
 */
namespace Jungle\Application\Dispatcher {

	/**
	 * Interface ReferenceInterface
	 * @package Jungle\Application\Dispatcher
	 */
	interface ReferenceInterface{

		/**
		 * @param $reference
		 * @return $this
		 */
		public function setReference($reference);

		/**
		 * @return mixed
		 */
		public function getReference();


	}
}

