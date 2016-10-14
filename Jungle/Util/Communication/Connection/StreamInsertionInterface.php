<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.10.2016
 * Time: 21:40
 */
namespace Jungle\Util\Communication\Connection {

	/**
	 * Interface StreamInsertionInterface
	 * @package Jungle\Util\Communication\Connection
	 */
	interface StreamInsertionInterface{

		/**
		 * @param StreamInterface $stream
		 * @return mixed
		 */
		public function insertTo(StreamInterface $stream);

	}
}

