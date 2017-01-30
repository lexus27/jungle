<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.12.2016
 * Time: 16:07
 */
namespace Jungle\Data\Record\Relation {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class RelationSchema
	 * @package Jungle\Data\Record\Relation
	 */
	abstract class RelationSchema extends Relation{

		/** @var array  */
		public $fields = [];

		/** @var  Schema */
		public $referenced_schema;

		/** @var array  */
		public $referenced_fields = [];

		/**
		 * @return array
		 */
		public function getLocalFields(){
			return $this->fields;
		}

		/**
		 * @param Record $record
		 * @param Record|null $related
		 * @return mixed
		 */
		abstract public function changeBackward(Record $record,Record $related = null);

		/**
		 * @return RelationSchema
		 */
		abstract public function getReferencedRelation();

		public function getFields(){
			return $this->fields;
		}

		/**
		 * @return array
		 */
		public function getReferencedFields(){
			return $this->referenced_fields;
		}

	}
}

