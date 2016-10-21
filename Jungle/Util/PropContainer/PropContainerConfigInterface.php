<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 19:07
 */
namespace Jungle\Util\PropContainer {
	
	use Jungle\Util\Exception\RequiredServiceParam;

	/**
	 * Interface PropContainerConfigInterface
	 * @package Jungle\Util
	 */
	interface PropContainerConfigInterface{

		/**
		 * @param $key
		 * @param null $default
		 * @param bool $createLinkIfDefault
		 * @return null
		 */
		public function &getConfig($key,$default = null, $createLinkIfDefault = false);

		/**
		 * @param $key
		 * @param $value
		 */
		public function setConfig($key,$value);

		/**
		 * @param $key
		 * @return mixed
		 * @throws RequiredServiceParam
		 */
		public function requireConfig($key);

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasConfig($key);

		/**
		 * @param $key
		 * @return $this
		 */
		public function rmConfig($key);

	}
}

