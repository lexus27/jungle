<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 23:47
 */
namespace Jungle\Util\Communication {

	/**
	 * Interface ConnectionInteractionInterface
	 * @package Jungle\Util\Communication
	 */
	interface ConnectionInteractionInterface{

		/**
		 * @param $data
		 * @return mixed
		 */
		public function send($data);

		/**
		 * @param $length
		 * @return mixed
		 */
		public function read($length);

	}
}

