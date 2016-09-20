<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 14:27
 */
namespace Jungle\Util\Data\Validation {
	
	/**
	 * Interface ExpertizeInterface
	 * @package Jungle\Util\Data\Validation
	 */
	interface ExpertizeInterface{

		/**
		 * @param $value
		 * @return MessageInterface|null
		 */
		public function expertize($value);

	}
}

