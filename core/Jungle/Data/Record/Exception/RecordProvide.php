<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 0:13
 */
namespace Jungle\Data\Record\Exception {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Exception;

	/**
	 * Class RecordProvide
	 * @package Jungle\Data\Record\Exception
	 */
	class RecordProvide extends Exception{

		/** @var  Record */
		protected $record;

		/**
		 * RecordProvide constructor.
		 * @param Record $record
		 * @param string $message
		 * @param int $code
		 * @param \Exception|null $previous
		 */
		public function __construct(Record $record, $message, $code = null, \Exception $previous = null){
			$this->record = $record;
			parent::__construct($message, $code,  $previous);
		}

		/**
		 * @return Record
		 */
		public function getRecord(){
			return $this->record;
		}

	}
}

