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
	 * Class RelationMany
	 * @package Jungle\Data\Record\Relation
	 */
	class RelationMany extends RelationAbstractHost{

		/**
		 * Как будет называться каждый объект коллекции в очередной итерации при использовании в контексте
		 * @var string|null
		 */
		public $each_name;



		/**
		 * RelationMany constructor.
		 * @param $name
		 * @param $referenced_relation
		 * @param null $referenced_schema_name
		 * @param null $each_name
		 */
		public function __construct($name, $referenced_relation, $referenced_schema_name = null, $each_name = null){
			parent::__construct($name, $referenced_relation, $referenced_schema_name);
			$this->each_name = $each_name;
		}

		/**
		 * @param Record $record
		 * @return Relationship
		 */
		public function load(Record $record){

			$this->_check();

			$ancestor = $this->referenced_schema->getCollection();
			$relationship = new Record\Relation\Relationship($record, $this, $ancestor );
			return $relationship;
		}

		/**
		 * @param Record $record
		 * @throws Record\Exception
		 */
		public function afterRecordCreate(Record $record){

			$this->_check();

			/** @var Relationship $relationship */
			$relationship = $record->getRelatedLoaded($this->name);
			if($relationship){
				// получаем данные из текущей записи, для выставления их в каждую связанную
				/** @var RelationForeign $opposite */
				$opposite = $this->referenced_relation;
				$data = $opposite->dataTo($record);

				// выставляем значения ссылки в каждый связанный объект
				foreach($relationship->getDirtyAddedItems() as $each){
					$each->assign($data);
					$each->save();// здесь сохранение пошло в схему с Foreign полем,
					// в нем вызовется beforeRecordSave где будет сохраняться $record
				}
				// изменения можно признать примененными
				$relationship->resetDirty();


				// требуется обновить условие relationship для свежих данных
				$relationship->applyCondition();
			}
		}


		public function afterRecordUpdate(Record $record, Snapshot $snapshot){

			$this->_check();

			/** @var Relationship $relationship */
			$relationship = $record->getRelated($this->name);
			$opposite = $this->getReferencedRelation();


			// нужно правильно обеспечить отсоединение записей от текущего Record
			foreach($relationship->getDirtyRemovedItems() as $each){
				// здесь под вопросом, т.к оппозитная загрузка пока не поддерживается
				if(! ($each->getRelatedLoaded($opposite->name) instanceof Record) ){
					if($this->master){
						$each->delete();
					}else{
						$each->assign($opposite->dataEmpty());
						$each->save();
					}
				}
			}
			$relationship->resetDirty();


			switch($opposite->on_update){

				case RelationForeign::ACTION_CASCADE:
					// изменить значения внешних ключей

					$data = $opposite->dataTo($record);
					try{
						$l = $relationship->getSyncLevel();
						if($opposite->virtual){
							$relationship->setSyncLevel($relationship::SYNC_STORE);
							$relationship->update($data);
						}else{
							$relationship->setSyncLevel($relationship::SYNC_FULL);
							$relationship->update($data,null,true);
						}
					}finally{
						$relationship->setSyncLevel($l);
					}


					break;
				case RelationForeign::ACTION_SETNULL:
					// сбросить связь
					$data = $opposite->dataEmpty();

					try{
						$l = $relationship->getSyncLevel();
						if($opposite->virtual){
							$relationship->setSyncLevel($relationship::SYNC_STORE);
							$relationship->update($data);
						}else{
							$relationship->setSyncLevel($relationship::SYNC_FULL);
							$relationship->update($data,null,true);
						}
					}finally{
						$relationship->setSyncLevel($l);
					}

					break;
				case RelationForeign::ACTION_RESTRICT:
					// запретить изменение
					if($opposite->virtual){
						if($record->hasChangesProperty($this->fields)){
							try{
								$l = $relationship->getSyncLevel();
								$relationship->setSyncLevel($relationship::SYNC_STORE);
								if($count = $relationship->count()){
									throw new \Exception(
										'Trying to change the fields that are referenced ' . $count .
										' related records from ' . $this->schema->getName() . '.' . $this->name
									);
								}


							}finally{
								$relationship->setSyncLevel($l);
							}
						}
					}
					break;

			}
			// это сохраняет все объекты данной коллекции
			$relationship->synchronize();
			$relationship->applyCondition();

		}


		public function beforeRecordDelete(Record $record){

			$this->_check();

			/** @var Relationship $relationship */
			$relationship = $record->getRelated($this->name);
			/** @var RelationForeign $opposite */
			$opposite = $this->referenced_relation;

			switch($opposite->on_delete){

				case RelationForeign::ACTION_CASCADE:
					// изменить значения внешних ключей
					try{
						$l = $relationship->getSyncLevel();
						if($opposite->virtual){
							$relationship->setSyncLevel($relationship::SYNC_STORE);
							$relationship->remove();
						}else{
							$relationship->setSyncLevel($relationship::SYNC_FULL);
							$relationship->remove();
						}
					}finally{
						$relationship->setSyncLevel($l);
					}

					// уничтожить relationship и все его подветки
					$relationship->setAncestor(null);

					break;
				case RelationForeign::ACTION_SETNULL:
					// сбросить связь
					$data = array_fill_keys($this->referenced_fields, null );

					try{
						$l = $relationship->getSyncLevel();
						if($opposite->virtual){
							$relationship->setSyncLevel($relationship::SYNC_STORE);
							$relationship->update($data);
						}else{
							$relationship->setSyncLevel($relationship::SYNC_FULL);
							$relationship->update($data,null,true);
						}
					}finally{
						$relationship->setSyncLevel($l);
					}

					// уничтожить relationship и все его подветки
					$relationship->setAncestor(null);

					break;
				case RelationForeign::ACTION_RESTRICT:
					// запретить удаление
					try{
						$l = $relationship->getSyncLevel();
						$relationship->setSyncLevel($relationship::SYNC_STORE);
						if($count = $relationship->count()){
							throw new \Exception('An attempt to delete a record that is referenced '.$count.' related records from '.$this->schema->getName().'.'.$this->name);
						}
					}finally{
						$relationship->setSyncLevel($l);
					}
					break;

			}
		}


	}
}

