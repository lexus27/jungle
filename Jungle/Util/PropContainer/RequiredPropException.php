<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 18:57
 */
namespace Jungle\Util\PropContainer {

	use Exception;

	/**
	 * Class RequiredPropException
	 * @package Jungle\Util\Exception
	 */
	class RequiredPropException extends \Exception{

		protected $key;

		protected $type;

		/**
		 * RequiredPropException constructor.
		 * @param string $param_name
		 * @param string $type
		 * @param string $message
		 * @param int $code
		 * @param Exception $previous
		 */
		public function __construct($param_name, $type, $message = '', $code = 0, Exception $previous = null){
			$this->key = $param_name;
			$this->type = $type;
			$message = 'Required srv "'.$type.'" with key "'.$param_name .'"'. ($message?"($message)":'');
			parent::__construct($message, $code, $previous);
		}

		/**
		 * @return string
		 */
		public function getKey(){
			return $this->key;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}


	}
}

