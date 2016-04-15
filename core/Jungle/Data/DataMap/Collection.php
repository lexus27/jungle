<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 17:30
 */
namespace Jungle\Data\DataMap {

	use Jungle\Data\Collection\Cmp;
	use Jungle\Data\DataMap\Collection\Context;
	use Jungle\Data\DataMap\Collection\Source;
	use Jungle\Data\DataMap\Criteria\CriteriaBlock;
	use Jungle\Data\DataMap\Criteria\CriteriaInterface;
	use Jungle\Data\DataMap\Schema;
	use Jungle\Data\DataMap\ValueAccess;
	use Jungle\Data\DataMap\ValueAccess\ValueAccessAwareInterface;
	use Jungle\Data\Registry\RegistryReadInterface;
	use Jungle\Util\Value\Cmp as UtilCmp;
	use Jungle\Util\Value\Massive;

	/**
	 * Class Collection
	 */
	class Collection implements \SeekableIterator, \Countable, \ArrayAccess, RegistryReadInterface, ValueAccessAwareInterface{

		/**
		 * @var   Schema|null
		 * Схема по которой работает коллекция
		 */
		protected $schema;

		/**
		 * @var   array
		 * Набор элементов коллекции
		 */
		protected $items = [];

		/**
		 * @var   Collection\Context|null
		 */
		protected $context;

		/**
		 * @var   Source
		 */
		protected $source;

		/**
		 * @var   Collection|null
		 */
		protected $ancestor;

		/**
		 * @var   Collection[]
		 */
		protected $descendants = [];


		/**
		 * @var   int|null
		 */
		protected $limit = -1;

		/**
		 * @var   int|null
		 */
		protected $offset = 0;

		/**
		 * @var   callable|Criteria|null
		 * Критерия содержимого.
		 */
		protected $criteria;


		/**
		 * @var   array|string
		 * @see sortBy
		 * Список полей по схеме с направлениями сортировки
		 */
		protected $sort_fields;

		/**
		 * @var  string[]|string
		 */
		protected $group_fields;


		/**
		 * @var callable
		 * Cache sorter
		 */
		protected $_sort_by_callback;

		/**
		 * @var  callable
		 * @see  sortBy
		 * Функция сортировки значения из набора данных
		 * взятого посредством $value_accessor из
		 * элемента коллекции или преобразованного набора данных
		 */
		protected $_sort_cmp;

		/**
		 * @param Source $source
		 * @return $this
		 */
		public function setSource(Source $source){
			$this->source = $source;
			$source->read();
			return $this;
		}

		/**
		 * @return Source
		 */
		public function getSource(){
			return $this->source;
		}

		/**
		 * @param Context $context
		 * @param bool|false $appliedInNew
		 * @param bool|false $appliedInOld
		 * @return $this
		 */
		public function setContext(Context $context, $appliedInNew = false, $appliedInOld = false){
			$old = $this->context;
			if($old !== $context){

			}
			return $this;
		}

		/**
		 * @return Context|null
		 */
		public function getContext(){
			return $this->context;
		}

		/**
		 * @param Collection|null $collection
		 * @param bool|false $appliedInNew
		 * @param bool|false $appliedInOld
		 * @return $this
		 */
		public function setAncestor(Collection $collection = null, $appliedInNew = false, $appliedInOld = false){
			$old = $this->ancestor;
			if($old !== $collection){
				$this->ancestor = $collection;
				if(!$appliedInOld && $old){
					$old->removeDescendant($this,true);
				}
				if(!$appliedInNew && $collection){
					$collection->addDescendant($this,true);
				}
			}
			return $this;
		}

		/**
		 * @return Collection|null
		 */
		public function getAncestor(){
			return $this->ancestor;
		}

		/**
		 * @param array $items
		 */
		public function setItems(array $items){
			$this->items = $items;
		}

		/**
		 * @return array
		 */
		public function getItems(){
			return $this->items;
		}

		/**
		 * @return bool
		 */
		public function isRoot(){
			return !$this->ancestor;
		}

		/**
		 * @return Collection
		 */
		public function getRoot(){
			if(!$this->ancestor){
				return $this;
			}else{
				return $this->ancestor->getRoot();
			}
		}



		/**
		 * @param Collection $collection
		 * @param bool|false $appliedIn
		 * @return $this
		 */
		public function addDescendant(Collection $collection, $appliedIn = false){
			if($this->searchDescendant($collection) === false){
				$this->descendants[] = $collection;
				if(!$appliedIn){
					$collection->setAncestor($this,true);
				}
			}
			return $this;
		}

		/**
		 * @param Collection $collection
		 * @return bool|int
		 */
		public function searchDescendant(Collection $collection){
			return array_search($collection, $this->descendants, true);
		}

		/**
		 * @param Collection $collection
		 * @param bool|false $appliedIn
		 * @return $this
		 */
		public function removeDescendant(Collection $collection, $appliedIn = false){
			if( ($i = $this->searchDescendant($collection)) === false ){
				array_splice($this->descendants, $i , 1);
				if(!$appliedIn){
					$collection->setAncestor(null,true,true);
				}
			}
			return $this;
		}




		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			$this->schema = $schema;
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getSchema(){
			if($this->ancestor && !$this->schema){
				return $this->ancestor->getSchema();
			}
			return $this->schema;
		}

		/**
		 * @param callable $criteria
		 * @param int|null $limit
		 * @param int|null $offset
		 * @return array
		 */
		public function collect(callable $criteria = null, $limit = null, $offset = null){
			if($limit===null)$limit = -1;
			if($offset===null)$offset = 0;
			$collected=[];$count=0;$i=0;
			foreach($this->items as & $item){
				if($criteria === null || call_user_func($criteria,$item,$this)){
					if($i >= $offset){$collected[] = $item;$count++;}
					$i++;
					if(!self::isNoLimited($limit) && $count >= $limit) break;
				}
			}
			return $collected;
		}

		/**
		 * @param callable|null $criteria
		 * @param null $offset
		 * @param null $limit
		 * @return array
		 */
		protected function _collect_references(callable $criteria = null, $limit = null, $offset = null){
			if($limit===null)$limit = -1;
			if($offset===null)$offset = 0;
			$collected=[];$count=0;$i=0;
			foreach($this->items as & $item){
				if($criteria === null || call_user_func($criteria,$item,$this)){
					if($i >= $offset){$collected[] = & $item;$count++;}
					$i++;
					if(!self::isNoLimited($limit) && $count >= $limit) break;
				}
			}
			return $collected;
		}


		/**
		 * @param int $limit
		 * @return bool
		 */
		public static function isNoLimited($limit = -1){
			return $limit === -1;
		}



		/**
		 * @param null $limit
		 * @return $this
		 */
		public function setLimit($limit = null){
			$limit = $limit!==null?intval($limit):$limit;
			if($this->limit!==$limit){
				$this->limit = $limit;
				$this->update();
			}
			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getLimit(){
			if($this->limit === null){
				if($this->ancestor){
					return $this->ancestor->getLimit();
				}else{
					return -1;
				}
			}else{
				return $this->limit;
			}
		}

		/**
		 * @param $offset
		 * @return $this
		 */
		public function setOffset($offset = null){
			$offset = $offset!==null?intval($offset):$offset;
			if($this->offset!==$offset){
				$this->offset = $offset;
				$this->update();
			}
			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getOffset(){
			if($this->offset === null){
				if($this->ancestor){
					return $this->ancestor->getOffset();
				}else{
					return 0;
				}
			}else{
				return $this->offset;
			}
		}

		/**
		 * Обновление записей из предков
		 * @param bool $propagate_descendants
		 */
		public function update( $propagate_descendants = true ){
			if($this->ancestor){
				$this->items = $this->ancestor->_collect_references(
					$this->getCriteria(),
					$this->getLimit(),
					$this->getOffset()
				);
			}
			if($propagate_descendants){
				foreach($this->descendants as $c){
					$c->update(true);
				}
			}
		}

		/**
		 * @param $criteria
		 * @param $offset
		 * @param $limit
		 * @return Collection
		 */
		public function extend($criteria = null, $offset = null, $limit = null){
			$descendant = clone $this;
			$descendant->ancestor           = $this;

			$descendant->offset             = $offset===null?0:$offset;
			$descendant->limit              = $limit===null?-1:$limit;
			$descendant->criteria           = Criteria::build($criteria);

			$descendant->update();

			$this->descendants[]            = $descendant;
			return $descendant;
		}

		protected function __clone(){
			$this->items            = [];
			$this->descendants      = [];
			$this->ancestor         = null;
		}


		/**
		 * @param $item
		 * @param bool|true $ancestor_include
		 * @return bool
		 */
		public function check($item, $ancestor_include = true){
			if($this->criteria && !call_user_func($this->criteria,$item,$this)){
				return false;
			}
			if($ancestor_include && $this->ancestor){
				return $this->ancestor->check($item);
			}
			return true;
		}


		/**
		 * @param CriteriaInterface|string|null $criteria
		 * @return $this
		 */
		public function setCriteria($criteria = null){
			if(!$criteria instanceof CriteriaInterface || !is_string($criteria) || !is_null($criteria)){
				throw new \InvalidArgumentException(
					'DataMapCollection contain criteria must be a string, null or CriteriaInterface instance'
				);
			}
			if($this->criteria !== $criteria){
				$this->criteria = $criteria;
				$this->update();
			}
			return $this;
		}

		/**
		 * @return CriteriaInterface|string|null
		 */
		public function getCriteria(){
			return $this->criteria;
		}

		/**
		 * @return CriteriaInterface
		 */
		public function getExtendedCriteria(){
			if(!$this->ancestor && $this->criteria){
				return $this->criteria;
			}
			$block = new CriteriaBlock();
			$object = $this;
			do{
				if($object->criteria){
					$block->addCondition($object->criteria);
					$block->addOperator('AND');
				}
			}while( ($object = $object->ancestor) );
			return $block->complete();
		}


		/**
		 * Метод послужит для генерации статических данных для объекта, который создается из этой коллекции
		 * исходя из указаний критерий в цепочке наследования
		 * Для того чтобы новый объект автоматически подходил к этой коллекции по критериям
		 *
		 * Условие для работоспособности, критерии должны быть производными CriteriaInterface
		 *
		 * @return array
		 */
		public function getExtendedCriteriaPredicates(){

		}

		/**
		 * @return array
		 * @see getPredicates
		 */
		public function create(){
			// get predicates from contain criteria hierarchy and set to data ;
			return [];
		}

		/**
		 * @return bool
		 */
		public function isEmpty(){
			return empty($this->items);
		}


		/**
		 * @param $identifier
		 * @return mixed
		 */
		public function get($identifier){
			$fieldName = $this->getPrimaryFieldName();
			if($fieldName!==null){
				$collected = $this->collect(function($item) use($fieldName,$identifier){
					if($this->valueAccessGet($item,$fieldName) === $identifier){
						return true;
					}
					return false;
				},null,1);
				return $collected?$collected[0]:null;
			}else{
				return null;
			}
		}

		/**
		 * @param $identifier
		 * @return bool
		 */
		public function has($identifier){
			return boolval($this->get($identifier));
		}


		/**
		 * @Event-Add-Propagate
		 * @param $item
		 * @return $this
		 */
		public function add($item){
			$root = $this->getRoot();
			if($root->_add($item,$this)){
				$this->_onSuccessAddedInitiator($item);
			}
			return $this;
		}

		/**
		 * @param $item
		 * @param Collection $initiator
		 * @return bool
		 * TODO сортировка при добавлении, либо определение правильной позиции нового элемента и проверка по лимитам
		 * TODO и смещению(Offset)
		 */
		protected function _add($item, Collection $initiator){
			if($this->check($item,false)){
				$count  = count($this->items);
				$this->items[$count] = $item;


				$itmRef = & $this->items[$count];
				$this->_onAdded($item,$initiator);
				foreach($this->descendants as $descendant){
					$descendant->_addByReference($itmRef, $count, $initiator);
				}
				return true;
			}
			return false;
		}


		/**
		 * @param $item
		 * @param $parentIndex
		 * @param Collection $initiator
		 */
		protected function _addByReference(&  $item, $parentIndex, Collection $initiator){
			$count = count($this->items);
			if($this->check($item,false) && $count < $this->getLimit() && $parentIndex >= $this->getOffset()){
				$this->items[$count] = & $item;
				$this->_onAdded($item,$initiator);
				foreach($this->descendants as $descendant){
					$descendant->_addByReference($item,$count,$initiator);
				}
			}
		}

		/**
		 * @Event-Remove-Propagate
		 * @param $item
		 */
		public function remove($item){
			$root = $this->getRoot();
			if($root->_remove($item,$this)){
				$this->_onSuccessRemovedInitiator($item);
			}
		}

		/**
		 * @param $item
		 * @param Collection $initiator
		 * @param bool $accessed
		 * @return bool
		 */
		public function _remove($item, Collection $initiator, $accessed = false){
			if($this->isRoot()){
				$i = array_search($item,$this->items,true);
				if($i !== false){
					array_splice($this->items,$i,1);
					$accessed = true;
				}else{
					return false;
				}
			}
			if($accessed){
				$this->_onRemoved($item,$initiator);
				foreach($this->descendants as $descendant){
					$descendant->_remove($item,$initiator,$accessed);
				}
			}
			return true;
		}


		/**
		 * @param $item
		 * @param Collection $initiator
		 */
		protected function _onAdded($item, Collection $initiator){

		}

		/**
		 * @param $item
		 * @param Collection $initiator
		 */
		protected function _onChanged($item, Collection $initiator){

		}

		/**
		 * @param $item
		 * @param Collection $initiator
		 */
		protected function _onRemoved($item, Collection $initiator){

		}

		protected function _onSuccessAddedInitiator($item){

		}

		protected function _onSuccessRemovedInitiator($item){

		}

		protected function _onSuccessChangedInitiator($item){

		}




		/**
		 * @return null|string
		 */
		public function getPrimaryFieldName(){
			$field = $this->getSchema()->getPrimary();
			return $field?$field->getName():null;
		}



		/**
		 * @param $data
		 * @param $required_field_name
		 * @return mixed
		 */
		public function valueAccessGet($data, $required_field_name){
			return $this->getSchema()->valueAccessGet($data,$required_field_name);
		}

		/**
		 * @param $data
		 * @param $required_field_name
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $required_field_name, $value){
			return $this->getSchema()->valueAccessGet($data,$required_field_name,$value);
		}

		/**
		 * @param $data
		 * @return array
		 */
		public function getRow($data){
			$row = [];
			foreach($this->getFieldNames() as $name){
				$row[] = $this->valueAccessGet($data,$name);
			}
			return $row;
		}


		/**
		 * @param $data
		 * @param array $fields
		 * @param bool|false $with_keys
		 * @return array
		 */
		public function getFieldValues($data, array $fields = null, $with_keys = false){
			if($fields===null) $fields = $this->getFieldNames();
			$values = [];
			foreach($fields as $i => $name){
				$values[$with_keys?$name:$i] = $this->valueAccessGet($data,$name);
			}
			return $values;
		}

		/**
		 * @return array
		 */
		public function getFieldNames(){

			return Massive::getNames($this->getSchema()->getFields());
		}




		/**
		 * @param callable $sorter
		 * @return $this
		 */
		public function setSortCmp(callable $sorter){
			$this->_sort_cmp = $sorter;
			return $this;
		}

		/**
		 * @return callable|\Closure|null
		 */
		public function getSortCmp(){
			if(!$this->_sort_cmp){
				$this->_sort_cmp = Cmp::getDefaultCmp();
			}
			return $this->_sort_cmp;
		}

		/**
		 * @param null $fields
		 * @return \Closure
		 */
		protected function createSortFunction($fields){
			return function($dataA,$dataB) use ($fields) {
				if($fields!==null){
					foreach($fields as $field_name => $direction){
						$returned = call_user_func(
							$this->getSortCmp(),
							$this->valueAccessGet($dataA,$field_name),
							$this->valueAccessGet($dataB,$field_name)
						);
						$result = $direction === 'DESC'?UtilCmp::invert($returned):$returned;
						if($result !== 0){
							return $result;
						}
					}
				}
				return 0;
			};
		}


		/**
		 * Sort this collection items
		 * @return $this
		 */
		public function sort(){
			if(!$this->_sort_by_callback){
				$this->_sort_by_callback = function($dataA,$dataB){
					$fields = (array)$this->getSortFields();
					if( ( $primary = $this->getPrimaryFieldName() ) ){
						$fields[$primary] = 'ASC';
					}
					foreach($fields as $field_name => $direction){
						$returned = call_user_func(
							$this->getSortCmp(),
							$this->valueAccessGet($dataA,$field_name),
							$this->valueAccessGet($dataB,$field_name)
						);
						$result = $direction === 'DESC'?UtilCmp::invert($returned):$returned;
						if($result !== 0){
							return $result;
						}
					}
					return 0;
				};
			}
			usort($this->items,$this->_sort_by_callback);
			return $this;
		}

		/**
		 * @param array $items
		 * @param null|array|string $sort_fields
		 * @return $this
		 */
		public function sortCustom(array & $items,$sort_fields = null){
			if($sort_fields!==null){
				$sort_fields = $this->prepareSortFields($sort_fields);
			}else{
				$sort_fields = $this->sort_fields;
			}
			usort($items,$this->createSortFunction($sort_fields));
			return $this;
		}

		/**
		 * @param array|string $fields
		 * @return array
		 */
		protected function prepareSortFields($fields){
			if(!is_null($fields)){
				if(!is_array($fields)){
					$fields = [$fields];
				}
				$_fields = [];
				foreach($fields as $field_name => $direction){
					if(is_int($field_name)){
						if(is_array($direction)){
							list($field_name,$direction) = $direction;
						}else{
							$field_name = $direction;
							$direction = null;
						}
					}
					if(!$direction){
						$direction = 'ASC';
					}
					$_fields[$field_name] = strtoupper($direction);
				}
				return $_fields;
			}else{
				return null;
			}
		}

		/**
		 * @param array|string $fields
		 *          (string) field_name
		 *          (array) [
		 *              [Field] => [Direction],
		 *              [Field] => [Direction]
		 *          ]
		 *          (array) [[Field],[Field]]
		 * @return $this
		 */
		public function sortBy($fields){
			if($fields === null){
				$fields = $this->getPrimaryFieldName();
			}
			$_fields = $this->prepareSortFields($fields);
			if($this->sort_fields!==$_fields){
				$this->sort_fields = $_fields;
				if($this->sort_fields){
					$this->sort();
				}
			}
			return $this;
		}

		/**
		 * TODO
		 * @param $fields
		 * @return $this
		 */
		public function groupBy($fields){
			$this->group_fields = $fields;
			return $this;
		}

		/**
		 * @return string[]
		 */
		public function getSortFields(){
			return $this->sort_fields;
		}

		/**
		 * @return string|string[]
		 */
		public function getGroupFields(){
			return $this->group_fields;
		}



		/** @var  int */
		protected $i;

		protected $root;

		/**
		 * @return mixed
		 */
		public function current(){
			return $this->items[$this->i];
		}

		/**
		 * Next data set
		 */
		public function next(){
			$this->i++;
		}

		/**
		 * @return int
		 */
		public function key(){
			return $this->i;
		}

		/**
		 * @return bool
		 */
		public function valid(){
			return isset($this->items[$this->i]);
		}

		/**
		 * Rewind iterator
		 */
		public function rewind(){
			$this->i = 0;
		}

		/**
		 * @param mixed $offset
		 * @return bool
		 */
		public function offsetExists($offset){
			return isset($this->items[$offset]);
		}

		/**
		 * @param mixed $offset
		 * @return null
		 */
		public function offsetGet($offset){
			return isset($this->items[$offset])?$this->items[$offset]:null;
		}

		/**
		 * @param mixed $offset
		 * @param mixed $value
		 */
		public function offsetSet($offset, $value){
			trigger_error('offsetSet not effect');
		}

		/**
		 * @param mixed $offset
		 */
		public function offsetUnset($offset){
			trigger_error('offsetUnset not effect');
		}

		/**
		 * @return int
		 */
		public function count(){
			return count($this->items);
		}

		/**
		 * @param int $position
		 */
		public function seek($position){
			$this->i = $position;
		}


	}
}

