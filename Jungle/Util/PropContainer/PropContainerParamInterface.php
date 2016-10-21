<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 15:51
 */
namespace Jungle\Util\PropContainer {

	use Jungle\Util\Exception\RequiredServiceParam;

	/**
	 * Interface PropContainerParamInterface
	 * @package Jungle\Util
	 */
	interface PropContainerParamInterface{

		/**
		 * @param $key
		 * @param null $default
		 * @param bool $createLinkIfDefault
		 * @return null
		 */
		public function &getOption($key,$default = null, $createLinkIfDefault = false);

		/**
		 * @param $key
		 * @param $value
		 */
		public function setOption($key,$value);

		/**
		 * @param $key
		 * @return mixed
		 * @throws RequiredServiceParam
		 */
		public function requireOption($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasOption($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function rmOption($key);

	}
}

