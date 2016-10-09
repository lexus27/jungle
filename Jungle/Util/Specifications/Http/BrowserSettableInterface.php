<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 16:10
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface BrowserSettableInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface BrowserSettableInterface{

		/**
		 * @param $userAgent
		 * @return $this
		 */
		public function setUserAgent($userAgent);


		/**
		 * @param array $languages
		 * @return mixed
		 */
		public function setDesiredLanguages(array $languages);

		/**
		 * @param array $media_types
		 * @return mixed
		 */
		public function setDesiredMediaTypes(array $media_types);

		/**
		 * @param $charset
		 * @return mixed
		 */
		public function setBestCharset($charset);

	}
}

