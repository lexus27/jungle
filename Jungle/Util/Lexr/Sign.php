<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.01.2017
 * Time: 0:27
 */
namespace Jungle\Util\Lexr {

	/**
	 * Class Sign
	 * @package Jungle\Util\Lexr
	 *
	 * Правило
	 */
	class Sign implements SignInterface{

		public $rule;


		/**
		 * @param Context $context
		 * @param int $position
		 * @param null $dialect
		 * @param null $nextPoint
		 * @return mixed
		 */
		public function recognize(Context $context, $position = 0, $dialect = null, & $nextPoint = null){
			// TODO: Implement recognize() method.
		}
	}
}

