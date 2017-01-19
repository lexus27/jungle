<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 13.11.2016
 * Time: 23:25
 */
namespace Jungle\Data\Record\Relation {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\Data\Record\Snapshot;

	/**
	 * Class Relation
	 * @package Jungle\Data\Record\Relation
	 */
	abstract class Relation{

		/** @var  string */
		public $name;

		/** @var  Schema */
		public $schema;

		/** @var  mixed */
		public $signature;

		/**
		 * Relation constructor.
		 * @param $name
		 */
		public function __construct($name){
			$this->name = $name;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param Record $record
		 * @return Record|null
		 */
		abstract public function load(Record $record);

		/**
		 * @param Record $record
		 * @return void
		 */
		public function beforeRecordCreate(Record $record){}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return void
		 */
		public function beforeRecordUpdate(Record $record, Snapshot $snapshot){}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return void
		 */
		public function beforeRecordSave(Record $record, Snapshot $snapshot = null){}

		/**
		 * @param Record $record
		 * @return void
		 */
		public function beforeRecordDelete(Record $record){}


		/**
		 * @param Record $record
		 * @return void
		 */
		public function afterRecordCreate(Record $record){}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return void
		 */
		public function afterRecordUpdate(Record $record, Snapshot $snapshot){}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return void
		 */
		public function afterRecordSave(Record $record, Snapshot $snapshot = null){}

		/**
		 * @param Record $record
		 * @return void
		 */
		public function afterRecordDelete(Record $record){}


		public function inspectContextEventsBefore(Record $record, array $changes){}

		public function inspectContextEventsAfter(Record $record, array $changes){}


		/**
		 * @param $name
		 * @return Record\Schema\Schema
		 * @throws \Exception
		 */
		public function getSchemaGlobal($name){
			if($name instanceof Schema){
				return $name;
			}
			return $this->schema->getRepository()->getSchema($name);
		}

		abstract public function initialize(Schema $schema);

		abstract public function getLocalFields();
	}
}

