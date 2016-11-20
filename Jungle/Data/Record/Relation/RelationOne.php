<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.11.2016
 * Time: 15:59
 */
namespace Jungle\Data\Record\Relation {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Snapshot;

	/**
	 * Class RelationOne
	 * @package Jungle\Data\Record\Relation
	 */
	class RelationOne extends RelationAbstractHost{

		/**
		 * @param Record $record
		 * @return mixed
		 */
		public function load(Record $record){
			$relation = $this->getReferencedRelation();
			return $relation->schema->loadFirst( $this->referencedCondition($record) );
		}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return mixed|void
		 * @throws Record\Exception
		 * @throws \Exception
		 */
		public function afterRecordSave(Record $record, Snapshot $snapshot = null){
			if($record->hasChangesProperty($this->name)){
				/** @var Record $related */
				$related = $record->getRelated($this->name);
				$data = $this->referenced_relation->dataTo($record);
				$related->assign($data);
				$related->save();
			}
		}

	}
}

