<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 17:14
 */
namespace Jungle\Data\Storage\Exception {

	/**
	 * Class AlreadyExists
	 * @package Jungle\Data\Storage\Exception
	 */
	class DuplicateEntry extends IndexViolation{
		/**
		 * IndexViolation constructor.
		 * @param string $key
		 * @param mixed $value
		 * @param string $systemMessage
		 * @param int $code
		 * @param \Exception|null $previous
		 */
		public function __construct($key, $value, $systemMessage = '', $code = 0, \Exception $previous = null){
			parent::__construct($key, $value, $systemMessage, $code, $previous);
		}
	}
}

