<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 12:47
 */
namespace Jungle\Util\Specifications\Hypertext {

	/**
	 * Interface ContentAwareInterface
	 * @package Jungle\Util\Specifications\Hypertext
	 */
	interface ContentAwareInterface{

		/**
		 * @param ContentInterface|string|null $content
		 * @return $this
		 */
		public function setContent($content = null);

		/**
		 * @return ContentInterface|string|null
		 */
		public function getContent();

	}
}

