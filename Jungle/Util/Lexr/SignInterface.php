<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.01.2017
 * Time: 0:47
 */
namespace Jungle\Util\Lexr {

	/**
	 * Interface SignInterface
	 * @package Jungle\Util\Lexr
	 */
	interface SignInterface{

		/**
		 * @param Context $context
		 * @param int $position
		 * @param null $dialect
		 * @param null $nextPoint
		 * @return mixed
		 */
		public function recognize(Context $context, $position = 0, $dialect = null, & $nextPoint = null);

	}
}

