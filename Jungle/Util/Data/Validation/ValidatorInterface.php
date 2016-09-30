<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 13:45
 */
namespace Jungle\Util\Data\Validation {

	/**
	 * Interface ValidatorInterface
	 * @package Jungle\Util\Data\Validation
	 */
	interface ValidatorInterface{

		/**
		 * @param $object
		 * @param array $parameters
		 * @return
		 */
		public function validate($object, array $parameters = []);

	}
}

