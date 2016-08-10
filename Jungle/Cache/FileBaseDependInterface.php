<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:38
 */
namespace Jungle\Cache {

	/**
	 * Interface FileBaseDependInterface
	 * @package Jungle\Cache
	 */
	interface FileBaseDependInterface{

		/**
		 * @return bool
		 */
		public function isTargetModify();

		/**
		 * @param array $files
		 * @param bool|false $merge
		 * @return mixed
		 */
		public function setTargetFiles(array $files, $merge = false);

	}
}

