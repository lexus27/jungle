<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.09.2016
 * Time: 12:58
 */
namespace Jungle\Data\Storage\Exception {

	/**
	 * Class IndexViolation
	 * @package Jungle\Data\Storage\Exception
	 */
	abstract class IndexViolation extends Operation{

		/** @var  string  */
		protected $key;

		/** @var  mixed  */
		protected $value;


		/**
		 * IndexViolation constructor.
		 * @param string $key
		 * @param mixed $value
		 * @param string $systemMessage
		 * @param int $code
		 * @param \Exception|null $previous
		 */
		public function __construct($key, $value, $systemMessage = '', $code = 0, \Exception $previous = null){
			$this->key = $key;
			$this->value = $value;
			parent::__construct($systemMessage, $code, $previous);
		}


		/**
		 * @return string
		 */
		public function getKey(){
			return $this->key;
		}

		/**
		 * @return mixed
		 */
		public function getValue(){
			return $this->value;
		}

	}
}

