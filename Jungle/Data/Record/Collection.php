<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:27
 */
namespace Jungle\Data\Record {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection\Sorter;
	use Jungle\Data\Record\Collection\SorterInterface;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Util\Data\Collection\ExtendableInterface;
	use Jungle\Util\Data\Collection\ExtendableTrait;
	use Jungle\Util\Data\Collection\Sortable\SorterInterface as SortableSorterInterface;
	use Jungle\Util\Data\Condition\Condition;
	use Jungle\Util\Data\Condition\ConditionBlock;
	use Jungle\Util\Data\Condition\ConditionComplex;
	use Jungle\Util\Data\Condition\ConditionInterface;
	use Jungle\Util\Data\Registry\RegistryReadInterface;
	use Jungle\Util\Data\Schema\OuterInteraction\SchemaAwareInterface;
	
	/**
	 * Class DataMapCollection
	 * @package modelX
	 *
	 * @property Collection|null $ancestor
	 */
	class Collection extends
		\Jungle\Util\Data\Collection\Enumeration\Collection
		implements CollectionInterface,
		ExtendableInterface,
		RegistryReadInterface,
		SchemaAwareInterface{

		const SAT_NONE   = false;
		const SAT_DEPLOY = 1;
		const SAT_LOAD   = 2;

		const SYNC_LOCAL    = 1;
		const SYNC_FULL     = 2;
		const SYNC_STORE    = 3;

		/** @var  Schema */
		protected $schema;

		/** @var  Collection */
		protected $root;

		/** @var array  */
		protected $root_present_identifiers = [];

		/** @var  int  */
		protected $saturation_mode = self::SAT_NONE;

		/** @var  Collection */
		protected $saturation_initiator;

		/** @var bool */
		protected $as_checkpoint = false;

		/** @var  Collection|null */
		protected $ancestor;

		/** @var  Collection[] */
		protected $descendants = [ ];

		/** @var bool  */
		protected $in_destructing = false;

		/** @var  Record[] */
		protected $items = [];

		/** @var  ConditionBlock|ConditionComplex */
		protected $contain_condition;

		/** @var  ConditionBlock|ConditionComplex */
		protected $extended_contain_condition;

		/** @var int  */
		protected $limit = -1;

		/** @var int  */
		protected $offset = 0;

		/** @var  Sorter */
		protected $sorter;

		/** @var  bool  */
		protected $auto_sort = false;

		/** @var  bool  */
		protected $sorted = false;


		/** @var  bool  */
		protected $auto_deploy = false;

		/** @var  bool  */
		protected $deployed = false;


		/** @var array  */
		protected $dirty_added = [];

		/** @var array  */
		protected $dirty_removed = [];

		/** @var bool  */
		protected $dirty_capturing = false;



		/** @var int */
		protected $sync_level = self::SYNC_LOCAL;



		/** @var  string|null  */
		protected $face_access_property = null;

		/**
		 * @param bool|true $capture
		 * @return $this
		 */
		public function setDirtyCapturing($capture = true){
			$this->dirty_capturing = $capture;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isDirtyCapturing(){
			return $this->dirty_capturing;
		}

		/**
		 *
		 */
		public function resetDirty(){
			$this->dirty_added = [];
			$this->dirty_removed = [];
		}

		/**
		 * @return Collection
		 */
		public function getCheckpoint(){
			if(!$this->ancestor){
				return $this;
			}else{
				if($this->as_checkpoint && $this->getRoot()->saturation_mode === self::SAT_NONE){
					return $this;
				}else{
					return $this->ancestor->getCheckpoint();
				}
			}
		}


		/**
		 * @return bool
		 */
		public function isDirty(){
			return $this->dirty_added || $this->dirty_removed;
		}

		/**
		 * @return Record[]
		 */
		public function getDirtyAddedItems(){
			return $this->dirty_added;
		}

		/**
		 * @return Record[]
		 */
		public function getDirtyRemovedItems(){
			return $this->dirty_removed;
		}


		/**
		 * @param bool|true $autoDeploy
		 * @return $this
		 */
		public function setAutoDeploy($autoDeploy = true){
			$this->auto_deploy = $autoDeploy;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isAutoDeploy(){
			return $this->auto_deploy;
		}

		/**
		 * @param bool|true $sort
		 * @return $this
		 */
		public function setAutoSort($sort = true){
			$this->auto_sort = $sort;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isAutoSort(){
			return $this->auto_sort;
		}

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			if($this->schema !== $schema){
				$this->schema = $schema;
			}
			return $this;
		}
		
		
		/**
		 * @return Schema
		 */
		public function getSchema(){
			if($this->schema){
				return $this->schema;
			}
			if($this->ancestor){
				return $this->ancestor->schema;
			}
			return $this->schema;
		}
		
		
		/**
		 * @param array|SorterInterface|SortableSorterInterface|null $sorter
		 * @return mixed
		 */
		public function setSorter($sorter = null){
			if(is_array($sorter)){
				$object = new Sorter();
				$object->setSortFields($sorter);
				$sorter = $object;
			}
			if($sorter!==null && !$sorter instanceof SorterInterface){
				throw new \LogicException('Need ' . SorterInterface::class);
			}
			$this->sorter = $sorter;
			if($sorter){
				$this->sorter->setAccess($this->schema);
			}
			return $this;
		}
		
		/**
		 * @return Sorter
		 */
		public function getSorter(){
			if($this->sorter){
				return $this->sorter;
			}elseif($this->ancestor && $this->ancestor->sorter){
				return $this->ancestor->sorter;
			}
			return null;
		}
		
		
		/**
		 * @return $this
		 */
		public function sort(){
			if($this->sorter){
				if(!$this->sorter->sort($this->items)){

				}
			}elseif($this->ancestor && $this->ancestor->sorter){
				$this->ancestor->sorter->sort($this->items);
			}
			$this->sorted = true;
			return $this;
		}
		
		
		/**
		 * @param ConditionInterface|array $condition
		 * @return $this
		 */
		public function setContainCondition($condition){
			$oldCondition = $this->contain_condition;
			$condition = Condition::build($condition);
			if($condition !== $oldCondition){
				$this->contain_condition = $condition;
				$this->_resetExtendedContainCondition();
			}
			return $this;
		}

		/**
		 * @record-listener
		 * @param Record $record
		 */
		public function itemCreated(Record $record){
			$root = $this->getRoot();
			$id = $record->getIdentifierValue();
			if(!in_array($id,$root->root_present_identifiers,true)){
				$root->root_present_identifiers[$id] = true;
			}
		}

		/**
		 * @param Record $record
		 */
		public function itemUpdated(Record $record){}

		/**
		 *
		 */
		protected function _resetExtendedContainCondition(){
			$this->extended_contain_condition = null;
			/** @var Collection $descendant */
			foreach($this->descendants as $descendant){
				$descendant->_resetExtendedContainCondition();
			}
		}


		/**
		 * @return ConditionBlock
		 */
		public function getContainCondition(){
			return $this->contain_condition;
		}

		/**
		 * @param $appendCondition
		 * @return ConditionComplex|bool
		 */
		public function getExtendedContainCondition($appendCondition = null){
			if($this->extended_contain_condition===null){
				$conditions = [];
				$o = $this;
				do{
					if($o->contain_condition){
						$conditions[] = $o->contain_condition;
					}
				}while(($o = $o->ancestor));
				$extended = null;
				if(!$conditions){
					$extended = null;
				}else if(count($conditions) > 1){
					$extended = $this->_appendCondition(null,$conditions,true);
				}else{
					$extended = $conditions[0];
				}
				if(!$extended){
					$this->extended_contain_condition = false;
				}else{
					$this->extended_contain_condition = $extended;
				}
			}
			$condition = $this->extended_contain_condition;
			if($condition){
				return $this->_appendCondition($condition,$appendCondition);
			}else{
				return Condition::build($appendCondition);
			}
		}


		
		/**
		 * @param Record $record
		 * @return bool
		 */
		public function checkCondition(Record $record){
			$condition = $this->getExtendedContainCondition();
			if($condition){
				return $condition($record);
			}
			return true;
		}
		
		/**
		 * @param $limit
		 * @return $this
		 */
		public function setLimit($limit = null){
			if($limit!==null && !is_numeric($limit)){
				throw new \InvalidArgumentException('Limit is wrong!');
			}
			if(self::isInfinityLimit($limit)){
				$limit = -1;
			}
			$old = $this->limit;
			if($old !== $limit){
				$this->limit = $limit;
				$this->_onLimitChanged($old);
			}
			return $this;
		}
		
		
		
		/**
		 * @return int
		 *
		 * Сопоставление - сумма всех лимитов  в цепочке наследования
		 *
		 */
		public function getLimit(){
			$limit = -1;
			if($this->ancestor){
				$limit = $this->ancestor->getLimit();
			}
			if($limit === -1 && $this->limit === -1){
				return -1;
			}
			if(!self::isInfinityLimit($limit) && $limit < $this->limit){
				throw new \LogicException('Limit is wrong for extended');
			}
			return $this->limit;
		}
		
		/**
		 * @param $limit
		 * @return bool
		 */
		public static function isInfinityLimit($limit){
			return $limit === -1 || $limit === null || $limit === 0 || $limit === false;
		}
		
		/**
		 * @param $offset
		 * @return $this
		 *
		 * Проверка выходит ли оффсет за пределы наследованных лимитов
		 *
		 */
		public function setOffset($offset){
			$old = $this->offset;
			$offset = intval($offset);
			if($old !== $offset){
				$this->offset = $offset;
				$this->_onOffsetChanged($old);
			}
			return $this;
		}
		
		/**
		 * @return int
		 */
		public function getOffset(){
			$offset = 0;
			if($this->ancestor){
				$offset = $this->ancestor->getOffset();
			}
			return $offset + $this->offset;
		}


		/**
		 * @return bool
		 */
		public function isRestricted(){
			return ($this->ancestor && $this->ancestor->isRestricted()) || ($this->contain_condition || $this->limit > 0 || $this->offset);
		}
		
		/**
		 * @return bool
		 */
		public function isEnough(){
			if(!self::isInfinityLimit($this->limit) && count($this->items) >= $this->limit){
				return true;
			}
			return false;
		}

		/**
		 * @return array
		 */
		public function getFieldNames(){
			return $this->schema->getFieldNames();
		}

		/**
		 * @FaceAccess
		 * @param $key
		 * @return mixed
		 */
		public function get($key){

			if($this->auto_deploy && !$this->deployed){
				$this->deploy();
			}
			$faceAccessProperty = $this->face_access_property;
			/** @var Record $item */
			if(!$faceAccessProperty){
				foreach($this->items as $item){
					if($item->getIdentifierValue() === $key){
						return $item;
					}
				}
			}else{
				foreach($this->items as $item){
					if($item->getProperty($faceAccessProperty) === $key){
						return $item;
					}
				}
			}
			return null;
		}
		
		/**
		 * @FaceAccess
		 * @param $key
		 * @return mixed
		 */
		public function has($key){
			if($this->auto_deploy && !$this->deployed){
				$this->deploy();
			}
			$faceAccessProperty = $this->face_access_property;
			/** @var Record $item */
			if(!$faceAccessProperty){
				foreach($this->items as $item){
					if($item->getIdentifierValue() === $key){
						return true;
					}
				}
			}else{
				foreach($this->items as $item){
					if($item->getProperty($faceAccessProperty) === $key){
						return true;
					}
				}
			}
			return false;
		}


		/**
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @return \Jungle\Data\Record[]
		 */
		public function collect($condition = null, $limit = null, $offset = null){

			if($this->auto_deploy && !$this->deployed){
				$this->deploy();
			}

			$condition = Condition::build($condition);
			$elapsed = 0;
			$count = 0;
			$a = [];
			foreach($this->items as $item){
				if(!$condition || call_user_func($condition,$item)){
					$elapsed++;
					if(!$offset || $elapsed>=$offset){
						$a[] = $item;

						if(++$count >= $limit){
							break;
						}

					}
				}
			}
			return $a;
		}

		/**
		 * @param ConditionInterface|null $condition
		 * @param null $offset
		 * @return Record|null
		 */
		public function collectOne($condition = null, $offset = null){
			$a = $this->collect($condition,1,$offset);
			return $a?$a[0]:null;
		}


		/**
		 * @return array
		 */
		public function getPresentIdentifiers(){
			if(!$this->ancestor){
				return array_keys($this->root_present_identifiers);
			}else{
				return $this->ancestor->getPresentIdentifiers();
			}
		}

		/**
		 * @param bool $state
		 * @param Collection|null $initiator
		 */
		public function saturate($state, Collection $initiator = null){
			$base = $this->getRoot();
			if(!$state){
				$base->saturation_mode = self::SAT_NONE;
				$base->saturation_initiator = null;
			}else{
				if(!$initiator){
					throw new \InvalidArgumentException('Initiator supplied for saturation is null, but must be Collection');
				}
				$base->saturation_mode = $state;
				$base->saturation_initiator = $initiator;
			}
		}

		/**
		 * @param Record $item
		 * @return bool
		 * ----Released-----
		 *   - Объект перед добавлением берется из схемы в свою очередь который
		 *      является приспособленцем до момента markRecordInitialized
		 *
		 *   - Требуется продумать вариант добавления записей при реализации запроса к хранилищу по Condition, Limit, etc
		 *      чтобы коллекция не производила проверки повторно
		 *
		 *   - есть возможность производить запрос к хранилищу с использованием исключения по айдишникам чтобы в ответ
		 *      не приходили присутствующие записи.
		 *
		 *   - контроль того чтобы запросы к хранилищу производились только при использовании коллекции или
		 *      наследников коллекции, это позволит избежать загрузки корневой коллекции которая возможно используется с
		 *      бесконечным лимитом, или сделать на безлимитные коллекции запрет на загрузку
		 * -----------------
		 */
		public function add($item){
			if(!$item instanceof Record){
				throw new \LogicException('$item Must be '.Record::class. ' instance!');
			}
			if($this->getCheckpoint()->_add($item)){
				if($item->getOperationMade() !== Record::OP_CREATE && $this->getRoot()->saturation_mode !== self::SAT_DEPLOY){
					$this->sorted = false;
				}
				return true;
			}
			return false;
		}

		/**
		 * @param Record $record
		 * @return $this
		 */
		protected function _add(Record $record){
			if($this->isEnough()){
				return false;
			}
			if($this->_beforeItemAdd($record) === false){
				return false;
			}
			$this->items[] = $record;
			$this->_afterItemAdd($record);
			return true;
		}

		/**
		 * @param Record $record
		 * @return bool
		 *
		 *
		 *
		 */
		protected function _beforeItemAdd($record){
			$schema = $record->getSchema();
			if(!$this->schema->isDerivativeFrom($schema)){
				throw new \LogicException(
					'Passed record schema "'.$schema->getName().
					'" is not support, collection use "'.$this->schema->getName().'" schema!');
			}
			if($record->getOperationMade() === Record::OP_CREATE && array_search($record,$this->items, true)!==false){
				return false;
			}

			$root = $this->getRoot();
			// Проверка присутствия, для загруженых потомков добавляемых не деплоем
			if(!$this->ancestor &&
			   $record->getOperationMade() !== $record::OP_CREATE &&
			   ($root->saturation_mode !== self::SAT_DEPLOY  || count($this->items))
			){
				$recordId = $record->getIdentifierValue();
				if(isset($root->root_present_identifiers[$recordId])){
					return false;
				}
			}
			//проверка на сопоставления условию Contain Condition
			if($root->saturation_mode === self::SAT_DEPLOY){
				if(!$this->isDerivative($root->saturation_initiator)){
					return false;
				}
				//если текущая коллекция является предком инициатора деплоя, то деплой был произведен унаследованым Contain Condition
			}elseif($record->getOperationMade()!==Record::OP_CREATE && !$this->checkCondition($record)){
				return false;
			}
			return true;
		}


		/**
		 * @param Record $record
		 */
		protected function _afterItemAdd($record){
			$satMode = $this->getRoot()->saturation_mode;
			$opMade = $record->getOperationMade();
			$record->markRecordInitialized();
			if($this->dirty_capturing && $satMode === self::SAT_NONE){
				$i = array_search($record,$this->dirty_removed,true);
				if($i !== false){
					array_splice($this->dirty_removed,$i,1);
				}else{
					$this->dirty_added[] = $record;
				}
			}

			if(!$this->ancestor && $opMade !== $record::OP_CREATE){
				$this->root_present_identifiers[$record->getIdentifierValue()] = true;
			}
			/** @var Collection $descendant */
			foreach($this->descendants as $descendant){
				$descendant->_add($record);
			}
		}

		/**
		 * @param $level
		 * @return $this
		 */
		public function setSyncLevel($level = self::SYNC_LOCAL){
			$this->getRoot()->sync_level = $level;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getSyncLevel(){
			return $this->getRoot()->sync_level;
		}

		protected static $special_record_call_function = null;
		protected static $special_record_get_function = null;
		protected static $special_record_set_function = null;
		protected static $special_record_static_get_function = null;
		protected static $special_record_static_set_function = null;

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordCallFunction(){
			if(self::$special_record_call_function===null){
				self::$special_record_call_function = \Closure::bind(function($record,$method,$arguments){
					return call_user_func_array([$record,$method],$arguments);
				},null,Record::class);
			}
			return self::$special_record_call_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordStaticGetFunction(){
			if(self::$special_record_static_get_function===null){
				self::$special_record_static_get_function = \Closure::bind(function($record,$property){
					return $record::${$property};
				},null,Record::class);
			}
			return self::$special_record_static_get_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordStaticSetFunction(){
			if(self::$special_record_static_set_function===null){
				self::$special_record_static_set_function = \Closure::bind(function($record,$property,$value){
					$record::${$property} = $value;
				},null,Record::class);
			}
			return self::$special_record_static_set_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordGetFunction(){
			if(self::$special_record_get_function===null){
				self::$special_record_get_function = \Closure::bind(function($record,$property){
					return $record->{$property};
				},null,Record::class);
			}
			return self::$special_record_get_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordSetFunction(){
			if(self::$special_record_static_set_function===null){
				self::$special_record_static_set_function = \Closure::bind(function($record,$property,$value){
					$record->{$property} = $value;
				},null,Record::class);
			}
			return self::$special_record_static_set_function;
		}


		/**
		 * @param $data
		 * @param null $condition
		 * @param bool $fullSyncIgnoreDirty
		 * @return $this
		 */
		public function update($data, $condition = null, $fullSyncIgnoreDirty = false){
			$level = $this->getSyncLevel();
			if($level === self::SYNC_STORE){
				$affected = $this->schema->storageUpdate($data, $this->getExtendedContainCondition($condition)->toStorageCondition());
				if($affected){
					foreach($this->getRoot()->collect($condition) as $item){
						$item->assign($data,null,null,false,false, true);
					}
					return $affected;
				}
			}elseif($level === self::SYNC_FULL){
				if($fullSyncIgnoreDirty){
					foreach($this->getRoot()->collect($condition) as $item){
						$item->assign($data,null,null,false,false, true);
					}
				}else{
					foreach($this->getRoot()->collect($condition) as $item){
						$item->assign($data);
					}
				}

			}else{
				$ad = $this->auto_deploy;
				if($ad){
					$this->auto_deploy = false;
				}
				foreach($this->collect($condition) as $item){
					$item->assign($data);
				}
				$this->auto_deploy = $ad;
			}
			return $this;
		}

		/**
		 * @SyncMemory
		 * @param $condition
		 * @return $this|int
		 */
		public function remove($condition = null){
			if(!is_array($condition) && $condition!==null){
				$condition = [$this->schema->getPrimaryFieldName() => $condition];
			}
			$level = $this->getSyncLevel();
			if($level === self::SYNC_STORE){
				$extended = $this->getExtendedContainCondition($condition);
				$affected = $this->schema->storageRemove($extended->toStorageCondition());
				if($affected){
					$this->getRoot()->_remove($extended);
					return $affected;
				}
			}elseif($level === self::SYNC_FULL){
				$this->getRoot()->_remove($this->getExtendedContainCondition($condition));
			}else{
				$this->getCheckpoint()->_remove($condition);
			}
			return $this;
		}

		/**
		 * @param $condition
		 */
		protected function _remove($condition = null){
			$condition = Condition::build($condition);
			foreach($this->items as $i => $item){
				if(!$condition || call_user_func($condition,$item)){
					if($this->_beforeItemRemove($item) !== false){
						array_splice($this->items,$i,1);
						$this->_afterItemRemove($item);
					}
				}
			}
		}

		/**
		 * @SyncMemory
		 */
		public function removeCurrent(){
			$current = $this->current();
			if($this->_beforeItemRemove($current)!==false){
				parent::removeCurrent();
				$this->_afterItemRemove($current);
			}
		}


		/**
		 * @param Record $record
		 * @return bool
		 */
		public function removeItem(Record $record){
			$level = $this->getSyncLevel();
			if($level === self::SYNC_STORE){
				if(!$record->delete()){
					return false;
				}
				if(!$this->getRoot()->_removeItem($record)){
					return false;
				}
				return true;
			}elseif($level === self::SYNC_FULL){
				return $this->getRoot()->_removeItem($record);
			}else{
				return $this->getCheckpoint()->_removeItem($record);
			}
		}



		/**
		 * @param Record $record
		 * @return bool
		 */
		protected function _removeItem($record){
			if($this->_beforeItemRemove($record) === false){
				return false;
			}
			$i = array_search($record, $this->items, true);
			if($i!==false){
				array_splice($this->items,$i,1);
				$this->_afterItemRemove($record);
				return true;
			}
			return false;
		}

		/**
		 * @param Record $record
		 */
		protected function _beforeItemRemove($record){}

		/**
		 * @param \Jungle\Data\Record $record
		 */
		protected function _afterItemRemove($record){
			if($this->dirty_capturing && $this->getSyncLevel() === self::SYNC_LOCAL){
				$i = array_search($record,$this->dirty_added,true);
				if($i !== false){
					array_splice($this->dirty_added,$i,1);
				}else{
					$this->dirty_removed[] = $record;
				}
			}
			if(!$this->ancestor && $record->getOperationMade() !== Record::OP_CREATE){
				unset($this->root_present_identifiers[$record->getIdentifierValue()]);
			}
			/** @var Collection $descendant */
			foreach($this->descendants as $descendant){
				$descendant->_removeItem($record);
			}
		}



		/**
		 * Отчистка коллекции в памяти, До последующего использования
		 */
		public function clean(){
			if(!$this->ancestor){
				$this->root_present_identifiers = [];
			}
			$this->items = [];
			$this->deployed = false;
			/** @var Collection $descendant */
			foreach($this->descendants as $descendant){
				$descendant->clean();
			}
		}

		/**
		 * SynchronizeException state
		 */
		public function synchronize(){
			foreach($this->items as $item){
				if($item->getOperationMade() === Record::OP_DELETE && !$this->in_destructing){
					$this->getRoot()->_removeItem($item);
				}elseif($item->hasChangesProperty()){
					if(!$item->save()){
						throw new Collection\SynchronizeException('Error save');
					}
				}
			}
		}

		/**
		 * Отчистка и последующее заполнение из родительской коллекции
		 * Актуализация по Родителю, Сбрасывает Dirty состояние
		 */
		public function refresh(){
			if($this->ancestor){
				$this->reset();
				$this->items = [];
				foreach($this->ancestor->items as $item){
					$this->_add($item);
				}
				/** @var Collection $descendant */
				foreach($this->descendants as $descendant){
					$descendant->refresh();
				}
			}
		}

		/**
		 *
		 */
		public function reset(){
			foreach($this->dirty_added as $item){
				$this->_removeItem($item);
			}
			$this->dirty_added = [];
			foreach($this->dirty_removed as $item){
				$this->_add($item);
			}
			$this->dirty_removed = [];
		}

		/**
		 * @param $condition
		 * @param int $offset
		 * @param null $orderBy
		 * @return Record|null
		 */
		public function single($condition, $offset = null, $orderBy = null){
			$record = null;
			$contain = $this->getContainCondition();
			$condition = $this->_appendCondition($contain,$condition);
			$record = $this->collectOne($condition,$offset);
			if($record){
				return $record;
			}
			if(!$this->auto_deploy && !$this->deployed){
				$this->saturate(self::SAT_DEPLOY,$this);
				$shipment = $this->schema->storageLoad($this->_prepareDeployStorageCondition($condition), 1, $this->getOffset() + $offset,$orderBy);
				if($shipment->count()){
					$item = $shipment->asAssoc()->fetch();
					if($item){
						$record = $this->schema->initializeRecord($item);
						if(!$this->add($record)){
							$record = null;
						}
					}
				}
				$this->saturate(false);
			}
			return $record;
		}

		/**
		 * Deploy collection from storage
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @return Collection
		 */
		public function deploy($condition = null, $limit = null, $offset = null, $orderBy = null){
			$this->saturate(self::SAT_DEPLOY,$this);
			if($this->auto_deploy){
				$condition = null; $limit = null; $offset = null; $orderBy = null;
			}
			if(!$this->deployed && !count($this->items)){
				$this->sorted = true;
			}
			$this->_deploy($condition, $limit, $offset, $orderBy);
			$this->saturate(false);
			$this->deployed = true;
			return $this;
		}

		/**
		 * @param ConditionBlock|null $condition
		 * @param $append
		 * @param bool|false $many
		 * @return ConditionBlock|ConditionComplex
		 */
		protected function _appendCondition(ConditionBlock $condition = null, $append, $many = false){
			if(!$condition){
				$condition = new ConditionComplex();
			}
			if($many){
				foreach($append as $app){
					$c = Condition::build($app);
					if($c) $condition->addCondition($c);
				}
			}else{
				$c = Condition::build($append);
				if($c) $condition->addCondition($c);
			}
			return $condition;
		}

		/**
		 * @param $condition
		 * @return array|null
		 */
		protected function _prepareDeployStorageCondition($condition){
			$condition = $this->getExtendedContainCondition($condition);
			$condition = $condition?$condition->toStorageCondition():[];
			if($existing = $this->getPresentIdentifiers()){
				$condition[] = [ $this->schema->getPrimaryFieldName(), 'NOT IN', $existing ];
			}
			return $condition;
		}



		/**
		 * @param null $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 */
		protected function _deploy($condition = null, $limit = null, $offset = null, $orderBy = null){
			if(!$orderBy && ($orderBy = $this->getSorter())){
				$orderBy = $orderBy->toStorageSortFields($this->schema);
			}
			$shipment = $this->schema->storageLoad($this->_prepareDeployStorageCondition($condition),$limit?:$this->getLimit(), $this->getOffset() + $offset , $orderBy);
			while(($item = $shipment->asAssoc()->fetch()) !== false){
				$this->add($this->schema->initializeRecord($item));
			}
		}

		/**
		 * @param Collection|ExtendableInterface|null|null $ancestor
		 * @param bool $appliedInNew
		 * @param bool $appliedInOld
		 * @return mixed
		 */
		public function setAncestor(ExtendableInterface $ancestor = null, $appliedInNew = false, $appliedInOld = false){
			$old = $this->ancestor;
			if($old !== $ancestor){
				$this->ancestor = $ancestor;
				if($old && !$appliedInOld){
					$old->removeDescendant($this, true);
				}
				if($ancestor && !$appliedInNew){
					$ancestor->addDescendant($this, true);
				}
				$ancestor->_onDelivery($this);
				$this->_onExtended();
			}
			$this->ancestor = $ancestor;
			return $this;
		}
		
		/**
		 * @return Collection
		 */
		public function getAncestor(){
			return $this->ancestor;
		}
		
		/**
		 * @param Collection|ExtendableInterface|ExtendableTrait $descendant
		 * @param bool $applied
		 * @return $this
		 */
		public function addDescendant(ExtendableInterface $descendant, $applied = false){
			if(array_search($descendant, $this->descendants, true) === false){
				$this->descendants[] = $descendant;
				if(!$applied) $descendant->setAncestor($this, true);
			}
			return $this;
		}
		
		/**
		 * @param Collection|ExtendableInterface|ExtendableTrait $descendant
		 * @return mixed
		 */
		public function searchDescendant(ExtendableInterface $descendant){
			return array_search($descendant, $this->descendants, true);
		}
		
		/**
		 * @param Collection|ExtendableInterface|ExtendableTrait $descendant
		 * @param bool $applied
		 * @return $this
		 */
		public function removeDescendant(ExtendableInterface $descendant, $applied = false){
			if(($i = array_search($descendant, $this->descendants, true)) !== false){
				array_splice($this->descendants, $i, 1);
				if(!$applied) $descendant->setAncestor(null, true, true);
			}
			return $this;
		}
		
		/**
		 * @return Collection[]
		 */
		public function getDescendants(){
			return $this->descendants;
		}
		
		/**
		 * @return Collection
		 */
		public function getRoot(){
			if(!$this->root){
				if($this->ancestor){
					$this->root = $this->ancestor->getRoot();
				}else{
					$this->root = $this;
				}
			}
			return $this->root;
		}

		/**
		 *
		 */
		public function rewind(){
			parent::rewind();
			if($this->auto_deploy && !$this->deployed){
				$this->deploy();
			}
			if($this->auto_sort && !$this->sorted){
				$this->sort();
			}
		}
		
		/**
		 * @return bool
		 */
		public function isRoot(){
			return !$this->ancestor;
		}

		/**
		 * @param null $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $sorter
		 * @return Collection
		 */
		public function extend($condition = null, $limit = null, $offset = null, $sorter = null){
			/** @var Collection $descendant */
			$descendant = clone $this;
			$descendant->setAncestor($this);
			$descendant->setLimit($limit);
			$descendant->setOffset($offset);
			$descendant->setContainCondition($condition);
			$descendant->setSorter($sorter);
			$descendant->refresh();
			return $descendant;
		}

		/**
		 * @param Collection $descendant
		 * @return bool
		 */
		public function isDerivative(Collection $descendant){
			if($descendant === $this){
				return true;
			}
			while($descendant = $descendant->getAncestor()){
				if($descendant === $this){
					return true;
				}
			}
			return false;
		}
		
		/**
		 *
		 */
		public function __clone(){
			$this->items = [];
			$this->ancestor = null;
			$this->descendants = [];
			$this->root_present_identifiers = [];
			$this->saturation_mode = self::SAT_NONE;
			$this->saturation_initiator;
			$this->auto_deploy = true;
			$this->auto_sort = true;
			$this->contain_condition = null;
			$this->extended_contain_condition = null;
			$this->face_access_property = null;
			$this->as_checkpoint = false;
			$this->dirty_capturing = false;
			$this->dirty_added = [];
			$this->dirty_removed = [];
			$this->limit = -1;
			$this->offset = 0;
		}



		/**
		 * @param Collection $descendant
		 */
		protected function _onDelivery($descendant){
			$descendant->schema = $this->schema;
		}

		/**
		 *
		 */
		protected function _onExtended(){}
		
		protected function _onLimitChanged($old){}
		
		protected function _onOffsetChanged($old){}

		/**
		 * @return string
		 */
		public function getInformationIdentifier(){
			return $this->getSchema()->getName() . '[COLLECTION]';
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getInformationIdentifier();
		}

		/**
		 * @throws \Jungle\Data\Record\Collection\SynchronizeException
		 */
		public function __destruct(){
			$this->in_destructing = true;
			if($this->auto_sync){
				$this->synchronize();
			}
		}

		/**
		 * @param null $condition
		 * @param null $offset
		 * @param null $limit
		 * @return int
		 */
		public function count($condition = null, $offset = null, $limit = null){
			$syncLevel = $this->getSyncLevel();
			if($syncLevel === self::SYNC_LOCAL){
				return $this->getCheckpoint()->_count($condition, $offset, $limit);
			}elseif($syncLevel === self::SYNC_FULL){
				return $this->getRoot()->_count($condition, $offset, $limit);
			}else{
				$schema = $this->getSchema();
				$condition = $this->getExtendedContainCondition($condition);
				$condition = $condition?$condition->toStorageCondition():[];
				return $schema->storageCount($condition,$limit?:$this->getLimit(), $this->getOffset() + $offset);
			}
		}

		/**
		 * @param $condition
		 * @param null $offset
		 * @param $limit
		 * @return int
		 */
		protected function _count($condition = null, $offset = null, $limit = null){
			if($condition === null && $offset === null && $limit === null){
				return count($this->items);
			}
			return count($this->collect($condition,$limit,$offset));
		}

		
		
	}
}

