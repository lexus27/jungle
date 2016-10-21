<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 23:13
 */
namespace Jungle\Util\Contents {

	/**
	 * Interface ContentsAwareInterface
	 * @package Jungle\Util
	 */
	interface ContentsAwareInterface{

		/**
		 * @return mixed
		 */
		public function getMediaType();

		/**
		 * @return string
		 */
		public function getBasename();

		/**
		 * @return mixed
		 */
		public function getSize();

		/**
		 * @return mixed
		 */
		public function getContents();

	}

}

