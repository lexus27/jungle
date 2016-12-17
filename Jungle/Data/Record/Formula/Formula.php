<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 0:05
 */
namespace Jungle\Data\Record\Formula {
	
	use Jungle\Data\Record;

	/**
	 * Class Formula
	 * @package Jungle\Data\Record\Formula
	 */
	abstract class Formula{

		protected $field;

		protected $empty_check = false;

		/**
		 * Formula constructor.
		 * @param $field
		 * @param bool|false $empty_check
		 */
		public function __construct($field,$empty_check = false){
			$this->field = $field;
			$this->empty_check = $empty_check;
		}

		/**
		 * @return mixed
		 */
		public function getField(){
			return $this->field;
		}

		/**
		 * @param Record $record
		 * @param $op_made
		 * @return bool
		 */
		public function check(Record $record, $op_made){
			$value = $record->getProperty($this->field);
			if($this->empty_check){
				return empty($value);
			}else{
				return isset($value);
			}
		}

		/**
		 * @param Record $record
		 * @param $op_made
		 * @return bool
		 */
		public function onSave(Record $record, $op_made){
			if($this->check($record,$op_made)){
				$record->setProperty($this->field, $this->fetch($record) );
				return true;
			}
			return false;
		}

		/**
		 * @param Record $record
		 * @return mixed
		 */
		abstract public function fetch(Record $record);

		/**
		 * @return array
		 */
		abstract public function getInvolvedFields();


	}
}

