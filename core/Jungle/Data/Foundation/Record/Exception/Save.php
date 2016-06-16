<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.06.2016
 * Time: 22:49
 */
namespace Jungle\Data\Foundation\Record\Exception {
	
	use Jungle\Data\Foundation\Record;
	use Jungle\Data\Foundation\Record\Exception;

	/**
	 * Class Save
	 * @package Jungle\Data\Foundation\Record\Exception
	 */
	class Save extends Exception{

		/** @var int  */
		protected $operation_made;

		/**
		 * Save constructor.
		 * @param string $message
		 * @param int $code
		 * @param null $previous
		 */
		public function __construct($message = '',$code = 0,$previous = null){
			$this->operation_made = $code;
			parent::__construct($message,$code,$previous);
		}

		/**
		 * @return int
		 */
		public function getOperationMade(){
			return $this->operation_made;
		}

	}
}

