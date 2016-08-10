<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 0:15
 */
namespace Jungle\Data\Record\Exception {
	
	use Jungle\Data\Record;

	/**
	 * Class Operation
	 * @package Jungle\Data\Record\Exception
	 */
	class Operation extends RecordProvide{

		/** @var int  */
		protected $operation;

		/**
		 * Operation constructor.
		 * @param Record $record
		 * @param int $operation
		 * @param string $message
		 * @param int $code
		 * @param \Exception|null $prev
		 */
		public function __construct(Record $record, $operation, $message, $code = 0, \Exception $prev = null){
			$this->operation = $operation;
			parent::__construct($record, $message,$code, $prev);
		}

		/**
		 * @return int
		 */
		public function getOperation(){
			return $this->operation;
		}

	}
}

