<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.12.2016
 * Time: 23:41
 */
namespace Jungle\Data\Record\Formula {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class FormulaCopy
	 * @package Jungle\Data\Record\Formula
	 */
	class FormulaCopy extends Formula{

		/** @var bool|false  */
		protected $source;

		/** @var bool|false  */
		protected $track_involved_change = false;

		/**
		 * FormulaCopy constructor.
		 * @param $field
		 * @param bool $empty_check
		 * @param $source
		 * @param bool|false $track_involved_change
		 */
		public function __construct($field, $source, $empty_check, $track_involved_change = false){
			$this->field = $field;
			$this->source = $source;
			$this->empty_collate = $empty_check;
			$this->track_involved_change = $track_involved_change;
		}

		/**
		 * @param Record $record
		 * @return Record|\Jungle\Data\Record[]|Record\Relation\Relationship|mixed
		 * @throws \Exception
		 */
		public function fetch(Record $record){
			return $record->getProperty($this->source);
		}

		/**
		 * @param Record $record
		 * @param $op_made
		 * @return bool
		 */
		public function check(Record $record, $op_made){
			$_ = parent::check($record, $op_made);
			return $_ || ($this->track_involved_change && $record->hasChangesProperty($this->getInvolvedFields()));
		}

		/**
		 * @return array
		 */
		public function getInvolvedFields(){
			return [$this->source];
		}

		/**
		 * @param Schema $schema
		 */
		public function attachToSchema(Schema $schema){
			// TODO: Implement attachToSchema() method.
		}
	}
}

