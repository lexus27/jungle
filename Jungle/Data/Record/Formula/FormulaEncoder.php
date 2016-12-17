<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 0:33
 */
namespace Jungle\Data\Record\Formula {
	
	use Jungle\Data\Record;


	/**
	 * @Decorator
	 * Class FormulaEncoder
	 * @package Jungle\Data\Record\Formula
	 */
	abstract class FormulaEncoder extends Formula{

		public $formula;

		/**
		 * FormulaEncoder constructor.
		 * @param Formula $formula
		 */
		public function __construct(Formula $formula){
			$this->formula = $formula;
		}

		/**
		 * @return mixed
		 */
		public function getField(){
			return $this->formula->getField();
		}

		/**
		 * @param Record $record
		 * @return mixed
		 */
		public function fetch(Record $record){
			$fetched = $this->formula->fetch($record);
			return $this->encodeFetched($fetched);
		}

		/**
		 * @param Record $record
		 * @param $op_made
		 * @return bool
		 */
		public function check(Record $record, $op_made){
			return $this->formula->check($record, $op_made);
		}

		/**
		 * @param Record $record
		 * @param $op_made
		 * @return bool
		 */
		public function onSave(Record $record, $op_made){
			if($this->formula->check($record,$op_made)){
				$record->setProperty($this->formula->field, $this->fetch($record) );
				return true;
			}
			return false;
		}

		/**
		 * @return array
		 */
		public function getInvolvedFields(){
			return $this->formula->getInvolvedFields();
		}

		/**
		 * @param $fetched
		 * @return mixed
		 */
		abstract public function encodeFetched($fetched);

	}
}

