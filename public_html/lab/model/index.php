<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.05.2016
 * Time: 22:34
 */
namespace modelX;

use Jungle\Basic\INamed;
use Jungle\DataOldRefactoring\DataMap\ValueAccess;
use Jungle\Util\Value\Callback;

include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'loader.php';


/** ---------------------------------------------------------------------------------------
 * ---------------   @Collections-Principals   ----------------------
 * ----------------------------------------------------------------------------*/

/**
 * Interface CollectionInterface
 * @package modelX
 */
interface CollectionInterface extends \Iterator{

	/**
	 * @param array $items
	 * @return $this
	 */
	public function setItems(array $items);

}

/**
 * Interface SortableCollectionInterface
 * @package modelX
 */
interface SortableCollectionInterface{

	/**
	 * @return mixed
	 */
	public function sort();

}

/**
 * Interface SorterAwareInterface
 * @package modelX
 */
interface SorterAwareInterface{

	/**
	 * @param $sorter
	 * @return mixed
	 */
	public function setSorter(SorterInterface $sorter = null);

	/**
	 * @return mixed
	 */
	public function getSorter();

}


/**
 * Interface OrderReadCollectionInterface
 * @package modelX
 */
interface OrderReadCollectionInterface{

	/**
	 * @param $start
	 * @param $length
	 * @return array
	 */
	public function slice($start, $length);

	/**
	 * @return mixed
	 */
	public function first();

	/**
	 * @return mixed
	 */
	public function last();

}

/**
 * Interface IndexedUniqueCollectionInterface
 * @package modelX
 */
interface IndexedUniqueCollectionInterface{

	/**
	 * @param $item
	 * @return mixed
	 */
	public function indexOf($item);

}

/**
 * Interface OrderedCollectionInterface
 * @package modelX
 */
interface OrderedCollectionInterface{

	/**
	 * @param $start
	 * @param $length
	 * @param null $replacement
	 * @return mixed
	 */
	public function splice($start, $length, $replacement = null);

	/**
	 * @param $item
	 * @return mixed
	 */
	public function append($item);

	/**
	 * @param $item
	 * @return mixed
	 */
	public function prepend($item);

	/**
	 * @param $offset
	 * @param $item
	 * @return mixed
	 */
	public function insert($offset, $item);

	/**
	 * @param $offset
	 * @param $item
	 * @return mixed
	 */
	public function replace($offset, $item);


	/**
	 * @param $offset
	 * @return mixed
	 */
	public function getByOffset($offset);


	/**
	 * @param $offset
	 * @return mixed
	 */
	public function whip($offset);

	/**
	 * @return mixed
	 */
	public function shift();

	/**
	 * @return mixed
	 */
	public function pop();


}

/**
 * Interface EnumerationCollectionInterface
 * @package modelX
 */
interface EnumerationCollectionInterface{

	/**
	 * @param $item
	 * @return mixed
	 */
	public function add($item);

}

/**
 * Interface EnumerationUniqueCollectionInterface
 * @package modelX
 */
interface EnumerationUniqueCollectionInterface{

	/**
	 * @param $item
	 * @param bool $checkUnique
	 * @return mixed
	 */
	public function add($item, $checkUnique = true);


	/**
	 * @param $item
	 * @return mixed
	 */
	public function remove($item);

}

/**
 * Interface AddingControlledInterface
 * @package modelX
 */
interface AddingControlledInterface{

	/**
	 * @param callable $hook
	 * @return mixed
	 */
	public function addAddingHook(callable $hook);

	/**
	 * @param callable $hook
	 * @return mixed
	 */
	public function removeAddingHook(callable $hook);

}

/**
 * Interface RemovingControlledInterface
 * @package modelX
 */
interface RemovingControlledInterface{

	/**
	 * @param callable $hook
	 * @return mixed
	 */
	public function addRemovingHook(callable $hook);

	/**
	 * @param callable $hook
	 * @return mixed
	 */
	public function removeRemovingHook(callable $hook);

}


/**
 * Class Collection
 * @package modelX
 */
abstract class Collection implements
	\Countable,
	CollectionInterface,
	OrderReadCollectionInterface{

	/** @var array */
	protected $items = [ ];

	/**
	 * @param array $items
	 * @return $this
	 */
	public function setItems(array $items){
		$this->items = $items;
		return $this;
	}

	/**
	 * @param $start
	 * @param $length
	 * @return mixed
	 */
	public function slice($start, $length){
		return array_slice($this->items, $start, $length, false);
	}

	/**
	 * @return mixed
	 */
	public function first(){
		list($k, $v) = each(array_slice($this->items, 0, 1, false));
		return $v;
	}

	/**
	 * @return mixed
	 */
	public function last(){
		list($k, $v) = each(array_slice($this->items, -1, 1, false));
		return $v;
	}


	/**
	 * @return mixed
	 */
	public function current(){
		return current($this->items);
	}

	/**
	 * @return mixed
	 */
	public function next(){
		next($this->items);
	}

	/**
	 * @return mixed
	 */
	public function key(){
		return key($this->items);
	}

	/**
	 * @return mixed
	 */
	public function valid(){
		return isset($this->items[key($this->items)]);
	}

	/**
	 * @return mixed
	 */
	public function rewind(){
		reset($this->items);
	}

	/**
	 * @return mixed
	 */
	public function count(){
		return count($this->items);
	}


}


/**
 * Class SortableCollection
 * @package modelX
 */
abstract class SortableCollection extends Collection implements
	SortableCollectionInterface,
	SorterAwareInterface{


	/** @var  SorterInterface */
	protected $sorter;

	/**
	 * @return mixed
	 */
	public function sort(){
		if($this->sorter){
			if(!$this->sorter->sort($this->items)){

			}
		}
		return $this;
	}

	/**
	 * @param $sorter
	 * @return mixed
	 */
	public function setSorter(SorterInterface $sorter = null){
		$this->sorter = $sorter;
		return $this;
	}

	/**
	 * @return DataMapSorterInterface
	 */
	public function getSorter(){
		return $this->sorter;
	}

}


/**
 * Class EnumerationCollection
 * @package modelX
 */
abstract class EnumerationCollection extends SortableCollection implements EnumerationCollectionInterface{

	/**
	 * @param $item
	 * @return $this
	 */
	public function add($item){
		$this->items[] = $item;
		return $this;
	}

}

/**
 * Class EnumerationUniqueCollection
 * @package modelX
 */
abstract class EnumerationUniqueCollection extends EnumerationCollection
	implements EnumerationUniqueCollectionInterface{

	/**
	 * @param $item
	 * @param bool|true $checkUnique
	 * @return $this
	 */
	public function add($item, $checkUnique = true){
		if($checkUnique){
			$index = array_search($item, $this->items, true);
			if($index !== false){
				return $this;
			}
		}
		$this->items[] = $item;
		return $this;
	}

	/**
	 * @param $item
	 * @return $this
	 */
	public function remove($item){
		$index = array_search($item, $this->items, true);
		if($index !== false){
			array_splice($this->items, $index, 1);
		}
		return $this;
	}


}

/**
 * Class OrderedCollection
 * @package modelX
 */
abstract class OrderedCollection extends Collection implements OrderedCollectionInterface{


	/**
	 * @param $start
	 * @param $length
	 * @param null $replacement
	 * @return array
	 */
	public function splice($start, $length, $replacement = null){
		return array_splice($this->items, $start, $length, $replacement);
	}

	/**
	 * @param $item
	 * @return $this
	 */
	public function append($item){
		$this->items[] = $item;
		return $this;
	}

	/**
	 * @param $item
	 * @return $this
	 */
	public function prepend($item){
		array_unshift($this->items, $item);
		return $this;
	}

	/**
	 * @param $offset
	 * @param $item
	 * @return $this
	 */
	public function insert($offset, $item){
		$this->splice($offset, 0, [ $item ]);
		return $this;
	}

	/**
	 * @param $offset
	 * @param $item
	 * @return $this
	 */
	public function replace($offset, $item){
		$this->items[$offset] = $item;
		return $this;
	}

	/**
	 * @param $offset
	 * @return mixed
	 */
	public function getByOffset($offset){
		return $this->items[$offset];
	}

	/**
	 * @param $offset
	 * @return mixed
	 */
	public function whip($offset){
		$items = $this->splice($offset, 1);
		return $items[0];
	}

	/**
	 * @return mixed
	 */
	public function shift(){
		return array_shift($this->items);
	}

	/**
	 * @return mixed
	 */
	public function pop(){
		return array_pop($this->items);
	}


}


/**
 * Interface CollectionExtendableInterface
 * @package modelX
 */
interface CollectionExtendableInterface{

	/**
	 * @return CollectionExtendableInterface
	 */
	public function extend();

}

interface CollectionExtendableDescendantsAwareInterface{

	/**
	 * @param CollectionAncestorAwareInterface $descendant
	 * @return mixed
	 */
	public function addDescendant(CollectionExtendable $descendant);

	/**
	 * @param CollectionAncestorAwareInterface $descendant
	 * @return mixed
	 */
	public function searchDescendant(CollectionExtendable $descendant);

	/**
	 * @param CollectionAncestorAwareInterface $descendant
	 * @return mixed
	 */
	public function removeDescendant(CollectionExtendable $descendant);

	/**
	 * @return CollectionAncestorAwareInterface[]
	 */
	public function getDescendants();

}

/**
 * Interface CollectionAncestorAwareInterface
 * @package modelX
 */
interface CollectionAncestorAwareInterface{

	/**
	 * @param CollectionExtendable|null $ancestor
	 * @return mixed
	 */
	public function setAncestor(CollectionExtendable $ancestor = null);

	/**
	 * @return CollectionExtendableInterface
	 */
	public function getAncestor();

}

/**
 * Class CollectionExtendable
 * @package modelX
 */
abstract class CollectionExtendable
	implements CollectionExtendableDescendantsAwareInterface, CollectionAncestorAwareInterface,
	CollectionExtendableInterface{

	/** @var  CollectionExtendable */
	protected $ancestor;

	/** @var  CollectionExtendable[] */
	protected $descendants = [ ];

	/**
	 * @return mixed
	 */
	public function extend(){
		$descendant = clone $this;
		$this->_onDelivery($descendant);
		return $descendant;
	}

	/**
	 * @param CollectionExtendable|null|null $ancestor
	 * @param bool $appliedInNew
	 * @param bool $appliedInOld
	 * @return mixed
	 */
	public function setAncestor(CollectionExtendable $ancestor = null, $appliedInNew = false, $appliedInOld = false){
		$old = $this->ancestor;
		if($old !== $ancestor){
			$this->ancestor = $ancestor;
			if($old && !$appliedInOld){
				$old->removeDescendant($this, true);
			}
			if($ancestor && !$appliedInNew){
				$ancestor->addDescendant($this, true);
			}
		}
		$this->ancestor = $ancestor;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAncestor(){
		return $this->ancestor;
	}

	protected function _onDelivery(CollectionAncestorAwareInterface $descendant){

	}

	/**
	 * @param CollectionAncestorAwareInterface|CollectionExtendable $descendant
	 * @return $this
	 */
	public function addDescendant(CollectionExtendable $descendant, $applied = false){
		if(array_search($descendant, $this->descendants, true) === false){
			$this->descendants[] = $descendant;
			if(!$applied) $descendant->setAncestor($this, true);
		}
		return $this;
	}

	/**
	 * @param CollectionAncestorAwareInterface|CollectionExtendable $descendant
	 * @return mixed
	 */
	public function searchDescendant(CollectionExtendable $descendant){
		// TODO: Implement searchDescendant() method.
	}

	/**
	 * @param CollectionAncestorAwareInterface|CollectionExtendable $descendant
	 * @return $this
	 */
	public function removeDescendant(CollectionExtendable $descendant, $applied = false){
		if(($i = array_search($descendant, $this->descendants, true)) !== false){
			array_splice($this->descendants, $i, 1);
			if(!$applied) $descendant->setAncestor(null, true, true);
		}
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescendants(){
		return $this->descendants;
	}


}


/**
 * Interface RecordCollectionInterface
 * @package modelX
 */
interface RecordCollectionInterface{

	/**
	 * @param SchemaOuterInteraction $schema
	 * @return $this
	 */
	public function setSchema(SchemaOuterInteraction $schema);

	/**
	 * @return SchemaOuterInteraction
	 */
	public function getSchema();

	/**
	 * @return array
	 */
	public function getFieldNames();

}

/**
 * Class DataMapCollection
 * @package modelX
 */
abstract class RecordCollection extends EnumerationCollection
	implements RecordCollectionInterface,
	SortableCollectionInterface,
	SorterAwareInterface{

	/** @var  SchemaOuterInteraction */
	protected $schema;


	/**
	 * @param SchemaOuterInteraction $schema
	 * @return $this
	 */
	public function setSchema(SchemaOuterInteraction $schema){
		if($this->schema !== $schema){
			$this->schema = $schema;
		}

		return $this;
	}

	/**
	 * @return SchemaOuterInteraction
	 */
	public function getSchema(){
		return $this->schema;
	}


	/**
	 * @return mixed
	 */
	public function sort(){
		if($this->sorter){
			if(!$this->sorter->sort($this->items)){

			}
		}
		return $this;
	}

	/**
	 * @param $sorter
	 * @return mixed
	 */
	public function setSorter(SorterInterface $sorter = null){
		if(!$sorter instanceof DataMapSorterInterface){
			throw new \LogicException('Need ' . DataMapSorterInterface::class);
		}
		$this->sorter = $sorter;
		$this->sorter->setAccess($this->schema);
		return $this;
	}

	/**
	 * @return DataMapSorterInterface
	 */
	public function getSorter(){
		return $this->sorter;
	}

	/**
	 * @return array
	 */
	public function getFieldNames(){
		return $this->schema->getFieldNames();
	}

}

/**
 * Class DataMapCollectionFrame
 * @package modelX
 */
abstract class RecordCollectionFrame extends RecordCollection{

	/** @var  DataMap */
	protected $frame;

	/**
	 * @return mixed
	 */
	public function current(){
		$frame = $this->_getFrame();
		$item = $this->items[key($this->items)];
		$frame->setOriginalData($item);
		return $frame;
	}

	/**
	 * @param SchemaOuterInteraction $schema
	 * @return $this
	 */
	public function setSchema(SchemaOuterInteraction $schema){
		parent::setSchema($schema);
		if($this->frame){
			$this->frame->setSchema($schema);
		}
		return $this;
	}


	/**
	 * @return DataMap
	 */
	protected function _getFrame(){
		if(!$this->frame){
			$this->frame = new DataMap();
			$this->frame->setImmutable(true, false);
			if($this->schema){
				$this->frame->setSchema($this->schema);
			}
		}
		return $this->frame;
	}
}

/**
 * Class DataMapCollectionPermanent
 * @package modelX
 */
abstract class RecordCollectionPermanent extends RecordCollection{

	/**
	 * @param $item
	 * @return $this|mixed
	 */
	public function add($item){
		$object = $this->prepareDataObject($item);
		$this->items[] = $object;
		return $this;
	}

	/**
	 * @param $originalData
	 * @return DataMap
	 */
	public function prepareDataObject($originalData){
		$dataMap = new DataMap();
		$dataMap->setSchema($this->schema);
		$dataMap->setImmutable(false);
		$dataMap->setOriginalData($originalData);
		return $dataMap;
	}
	
}

/**
 * Class ModelCollection
 * @package modelX
 */
abstract class ModelCollection extends RecordCollectionPermanent{

	/** @var  ModelSchema */
	protected $schema;

	/**
	 * @param $data
	 * @return $this
	 */
	public function load($data){
		$object = $this->prepareDataObject($data, Model::OP_MADE_READY);
		$this->items[] = $object;
		return $this;
	}

	/**
	 * @param SchemaOuterInteraction $schema
	 * @return $this
	 */
	public function setSchema(SchemaOuterInteraction $schema){
		if(!$schema instanceof ModelSchema){
			throw new \LogicException('Need ' . ModelSchema::class);
		}
		parent::setSchema($schema);
		$schema->setBaseCollection($this);
		return $this;
	}

	/**
	 * @param $data
	 * @param int $operationMade
	 * @return Model
	 */
	public function prepareDataObject($data, $operationMade = Model::OP_MADE_DETACHED){
		$model = $this->schema->createModel();
		$model->initialFill($data, $operationMade);
		return $model;
	}

}


/**
 * Interface CmpInterface
 * @package Jungle\Data\Collection
 */
interface CmpInterface{

	/**
	 * @param $current_value
	 * @param $selection_each
	 * @return int
	 */
	public function __invoke($current_value, $selection_each);

}

/**
 * Class Cmp
 * @package Jungle\Data\Collection
 */
class Cmp{

	/** @var callable[] */
	protected static $cmp_collection = [ ];

	/**
	 * @return callable|\Closure|null
	 */
	public static function getDefaultCmp(){
		return self::getCmpByAlias('default');
	}

	/**
	 * @param $alias
	 * @return null|callable
	 */
	public static function getCmpByAlias($alias){
		if(isset(self::$cmp_collection[$alias])){
			return self::$cmp_collection[$alias];
		}elseif($alias === 'default'){
			self::$cmp_collection[$alias] = function ($a, $b){
				if($a == $b){
					return 0;
				}
				return $a < $b ? -1 : 1;
			};
			return self::$cmp_collection[$alias];
		}
		return null;
	}

	/**
	 * @param $alias
	 * @param callable $cmp
	 */
	public static function setCmpByAlias($alias, callable $cmp){
		self::$cmp_collection[$alias] = self::checkoutCmp($cmp);
	}

	/**
	 * @param $cmp
	 * @return callable|null
	 */
	public static function checkoutCmp($cmp = null){
		if($cmp === null){
			return self::getDefaultCmp();
		}
		return Callback::checkoutCallableInstanceOrString(
			'Cmp',
			CmpInterface::class,
			function ($string){
				return self::getCmpByAlias($string);
			},
			$cmp
		);
	}


}

/**
 * Interface SorterInterface
 * @package Jungle\Data
 */
interface SorterInterface{

	/**
	 * @return callable
	 */
	public function getCmp();

	/**
	 * @param callable $cmp
	 * @return mixed
	 */
	public function setCmp(callable $cmp);

	/**
	 * @param array $array
	 * @return $this
	 */
	public function sort(array & $array);

}

/**
 * Class Sorter
 * @package Jungle\Data
 */
class Sorter implements SorterInterface{

	/** @var  callable|null */
	protected $cmp;

	/**
	 * @return callable|\Closure|null
	 */
	public function getCmp(){
		if(!$this->cmp){
			$this->cmp = Cmp::getDefaultCmp();
		}
		return $this->cmp;
	}

	/**
	 * @param callable|null $cmp
	 * @return $this
	 */
	public function setCmp(callable $cmp = null){
		$this->cmp = Cmp::checkoutCmp($cmp);
		return $this;
	}

	/**
	 * @param $array
	 * @return bool
	 */
	public function sort(array & $array){
		return usort($array, $this->cmp);
	}
}

/**
 * Interface DataMapSorterInterface
 * @package modelX
 */
interface DataMapSorterInterface{

	/**
	 * @param OuterValueAccessAwareInterface|null $access
	 * @return mixed
	 */
	public function setAccess(OuterValueAccessAwareInterface $access = null);

	/**
	 * @return mixed
	 */
	public function getAccess();

	/**
	 * @param $fields
	 * @return mixed
	 */
	public function setSortFields($fields);

	/**
	 * @return mixed
	 */
	public function getSortFields();

}

/**
 * Class DataMapSorter
 * @package modelX
 */
class DataMapSorter extends Sorter implements DataMapSorterInterface{

	/** @var  array */
	protected $sort_fields;

	/** @var  OuterValueAccessAwareInterface */
	protected $accessor;

	/**
	 * @param array $items
	 * @return $this
	 */
	public function sort(array & $items){
		return usort(
			$items,
			function ($a, $b){
				$result = 0;
				$fields = $this->getSortFields();
				foreach($fields as $field_name => $direction){
					$valueA = OuterValueAccess::handleAccessGet($this->accessor, $a, $field_name);
					$valueB = OuterValueAccess::handleAccessGet($this->accessor, $b, $field_name);
					$result = call_user_func($this->cmp, $valueA, $valueB);
					if($result !== 0){
						$result = $direction === 'DESC' ? \Jungle\Util\Value\Cmp::invert($result) : $result;
						break;
					}
				}
				return $result;
			}
		);
	}

	/**
	 * @param $fields
	 * @return $this
	 */
	public function setSortFields($fields){
		$this->sort_fields = $fields;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getSortFields(){
		return $this->sort_fields;
	}

	/**
	 * @param OuterValueAccessAwareInterface $accessor
	 * @return $this
	 */
	public function setAccess(OuterValueAccessAwareInterface $accessor = null){
		$this->accessor = $accessor;
		return $this;
	}

	/**
	 * @return OuterValueAccessAwareInterface
	 */
	public function getAccess(){
		return $this->accessor;
	}

}


/** ----------------------------------------------------------------
 * ---------------------   @Schema-Principals   ------------------
 * --------------------------------------------------------------------*/


/**
 * Interface SchemaMetaInterface
 * @package modelX
 */
interface SchemaMetaInterface{

	/**
	 * @return string[]
	 */
	public function getFieldNames();

}

/**
 * Interface SchemaInterface
 * @package modelX
 */
interface SchemaInterface extends SchemaMetaInterface{

	/**
	 * @param $name
	 * @return FieldInterface
	 */
	public function getField($name);

	/**
	 * @param FieldInterface|string $field
	 * @return mixed
	 */
	public function getFieldIndex($field);

	/**
	 * @param $index
	 * @return FieldInterface|null
	 */
	public function getFieldByIndex($index);

	/**
	 * @return string[]|int[]
	 */
	public function getFieldNames();

	/**
	 * @return FieldInterface[]
	 */
	public function getFields();

}


/**
 * Interface SchemaMetaIndexedInterface
 * @package modelX
 */
interface SchemaMetaIndexedInterface{

	/**
	 * @return string
	 */
	public function getPrimaryFieldName();

	/**
	 * @param string $fieldName
	 * @return bool
	 */
	public function isPrimaryField($fieldName);

	/**
	 * @param string $fieldName
	 * @return bool
	 */
	public function isUniqueField($fieldName);

}

/**
 * Interface SchemaIndexedInterface
 * @package modelX
 */
interface SchemaIndexedInterface extends SchemaMetaIndexedInterface{

	/**
	 * @return FieldInterface
	 */
	public function getPrimaryField();

	/**
	 * @param FieldInterface|string $field
	 * @return bool
	 */
	public function isPrimaryField($field);

	/**
	 * @param FieldInterface|string $field
	 * @return bool
	 */
	public function isUniqueField($field);

	/**
	 * @param $name
	 * @return IndexInterface|null
	 */
	public function getIndex($name);

	/**
	 * @return IndexInterface[]
	 */
	public function getIndexes();
}

/**
 * Interface FieldInterface
 * @package modelX
 * Без геттеров и сеттеров для значения
 */
interface FieldInterface{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return mixed
	 */
	public function getDefault();

	/**
	 * @return bool
	 */
	public function isNullable();

	/**
	 * @return SchemaInterface
	 */
	public function getSchema();

	/**
	 * @param SchemaInterface $schema
	 * @return mixed
	 */
	public function setSchema(Schema $schema);

}

/**
 * Interface FieldIndexedInterface
 * @package modelX
 */
interface FieldIndexedInterface{

	/**
	 * @return bool
	 */
	public function isPrimary();

	/**
	 * @return bool
	 */
	public function isUnique();

}


/**
 * Interface FieldVisibilityControlInterface
 * @package modelX
 */
interface FieldVisibilityControlInterface{

	/**
	 * @return bool
	 */
	public function isReadonly();

	/**
	 * @return bool
	 */
	public function isPrivate();

}

/**
 * Interface IndexInterface
 * @package modelX
 */
interface IndexInterface{

	const TYPE_PRIMARY = 1;

	const TYPE_UNIQUE = 2;

	const TYPE_KEY = 3;

	const TYPE_SPATIAL = 4;

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return array
	 */
	public function getFieldNames();

	/**
	 * @param string $field_name
	 * @return int|null
	 */
	public function getFieldSize($field_name);

	/**
	 * @param string $field_name
	 * @return int|null
	 */
	public function getFieldDirection($field_name);

	/**
	 * @param string $field_name
	 * @return bool
	 */
	public function hasField($field_name);

}

/**
 * Class Schema
 * @package modelX
 */
abstract class Schema implements SchemaInterface, SchemaIndexedInterface{

	/** @var  FieldInterface[] */
	protected $fields = [ ];

	/** @var  IndexInterface[] */
	protected $indexes = [ ];

	/**
	 * @param $name
	 * @inheritDoc
	 */
	public function getField($name){
		foreach($this->fields as $field){
			if($field->getName() === $name){
				return $field;
			}
		}
		return null;
	}


	/**
	 * @param FieldInterface|string $field
	 * @return mixed
	 */
	public function getFieldIndex($field){
		return array_search($field, $this->fields, true);
	}

	/**
	 * @param $index
	 * @return FieldInterface
	 */
	public function getFieldByIndex($index){
		return $this->fields[$index];
	}

	/**
	 * @return array
	 */
	public function getFieldNames(){
		$names = [ ];
		foreach($this->fields as $field){
			$names[] = $field->getName();
		}
		return $names;
	}


	/**
	 * @param FieldInterface $field
	 * @return $this
	 */
	public function addField(FieldInterface $field){
		if($this->beforeAddField($field)!==false){
			$name = $field->getName();
			foreach($this->fields as $f){
				if($f->getName() === $name){
					throw new \LogicException('Field "'.$name.'" already exists');
				}
			}
			$this->fields[] = $field;
			$field->setSchema($this);
		}
		return $this;
	}

	/**
	 * @param $field
	 */
	protected function beforeAddField($field){}

	/**
	 * @inheritDoc
	 */
	public function getFields(){
		return $this->fields;
	}


	/**
	 * @return string
	 */
	public function getPrimaryFieldName(){
		foreach($this->fields as $field){
			foreach($this->indexes as $index){
				$name = $field->getName();
				if($index->hasField($name) && $index->getType() === $index::TYPE_PRIMARY){
					return $name;
				}
			}
		}
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getPrimaryField(){
		foreach($this->fields as $field){
			foreach($this->indexes as $index){
				if($index->hasField($field->getName()) && $index->getType() === $index::TYPE_PRIMARY){
					return $field;
				}
			}
		}
		return null;
	}

	/**
	 * @param FieldInterface|string $field
	 * @return bool
	 */
	public function isPrimaryField($field){
		if($field instanceof FieldInterface) $field = $field->getName();
		foreach($this->indexes as $index){
			if($index->hasField($field) && $index->getType() === $index::TYPE_PRIMARY){
				return true;
			}
		}
		return false;
	}

	/**
	 * @param FieldInterface|string $field
	 * @return bool
	 */
	public function isUniqueField($field){
		if($field instanceof FieldInterface) $field = $field->getName();
		foreach($this->indexes as $index){
			if($index->hasField($field) && $index->getType() === $index::TYPE_UNIQUE){
				return true;
			}
		}
		return false;
	}

	/**
	 * @param IndexInterface $index
	 * @return $this
	 */
	public function addIndex(IndexInterface $index){
		$name = $index->getName();
		foreach($this->indexes as $i){
			if($i->getName() === $name){
				throw new \LogicException('Index name "'.$name.'" already exists in schema!');
			}
		}

		$this->indexes[] = $index;

		return $this;
	}

	/**
	 * @param $name
	 * @return IndexInterface|null
	 */
	public function getIndex($name){
		foreach($this->indexes as $index){
			if($index->getName() === $name){
				return $index;
			}
		}
		return null;
	}

	/**
	 * @return IndexInterface[]
	 */
	public function getIndexes(){
		return $this->indexes;
	}


}

/**
 * Class Field
 * @package modelX
 */
abstract class Field implements FieldInterface, FieldIndexedInterface{

	/** @var  Schema */
	protected $schema;

	/** @var  string */
	protected $name;

	/** @var  string */
	protected $type = 'string';

	/** @var  null */
	protected $default = null;

	/** @var  bool */
	protected $nullable = false;

	/**
	 * Field constructor.
	 * @param $name
	 * @param string $type
	 */
	public function __construct($name, $type = null){
		$this->name = $name;
		$this->type = $type?:'string';
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
		return $this->schema;
	}


	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType(){
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function getDefault(){
		return $this->default;
	}

	/**
	 * @return bool
	 */
	public function isNullable(){
		return $this->nullable;
	}


	/**
	 * @return bool
	 */
	public function isPrimary(){
		return $this->schema->isPrimaryField($this->name);
	}

	/**
	 * @return bool
	 */
	public function isUnique(){
		return $this->schema->isPrimaryField($this->name);
	}

}

interface ValueToolInterface{

	public function valueNormalize($value);

	public function valueSanitize($value);

	public function valueValidate($value);

	public function valueStringify($value);

}

/**
 * Interface ValueTransientInterface
 * @package modelX
 */
interface ValueTransientInterface{

	/**
	 * Взбодрить
	 * @param $value
	 * @return mixed
	 */
	public function unpack($value);

	/**
	 * Усыпить
	 * @param $value
	 * @return mixed
	 */
	public function pack($value);

}

/**
 * Interface ValueValidatorInterface
 * @package modelX
 */
interface ValueValidatorInterface{

	/**
	 * @param $value
	 * @return mixed
	 */
	public function validate($value);

}

/**
 * Class FieldValueUtil
 * @package modelX
 */
abstract class FieldParticipant extends Field{

	/**
	 * @param $value
	 * @return mixed
	 */
	public function valueNormalize($value){
		settype($value, $this->type);
		return $value;
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function valueSanitize($value){
		return $value;
	}

	/**
	 * @param $value
	 * @return bool
	 */
	public function valueValidate($value){
		if(!$this->nullable && $value === null){
			return false;
		}
		return true;
	}

	/**
	 * @param $value
	 * @return string
	 */
	public function valueStringify($value){
		return (string) $value;
	}

}

/**
 * Class Index
 * @package modelX
 */
abstract class Index implements IndexInterface, INamed{

	/** @var  string */
	protected $name;

	/** @var  string */
	protected $type;

	/** @var array */
	protected $fields = [];


	/**
	 * @param $field_name
	 * @param null $size
	 * @param null $direction
	 * @return $this
	 */
	public function addField($field_name, $size = null, $direction = null){
		$this->fields[$field_name] = [ $size, $direction ?: 'ASC' ];
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @param $type
	 * @return $this
	 */
	public function setType($type){
		$this->type = $type;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getType(){
		return $this->type;
	}

	/**
	 * @inheritDoc
	 */
	public function getFieldNames(){
		return array_keys($this->fields);
	}

	/**
	 * @inheritDoc
	 */
	public function getFieldSize($field_name){
		return isset($this->fields[$field_name]) ? $this->fields[$field_name][1] : null;
	}

	/**
	 * @inheritDoc
	 */
	public function getFieldDirection($field_name){
		return isset($this->fields[$field_name]) ? $this->fields[$field_name][1] : null;
	}

	/**
	 * @inheritDoc
	 */
	public function hasField($field_name){
		return array_key_exists($field_name, $this->fields);
	}

}


/** ---------------------------------------------------------------------------------
 * -------------------   @Outer-Interaction-Principals   --------------------------
 * ---------------------------------------------------------------------------------*/

/**
 * @Outer-interaction
 * Оригинал находится отдельно от точки обработки данных,
 * поля под псевдонимами вытягиваются из оригинала
 * Мы ничего не знаем о оригинале если он объект класса или поведенческая часть программы.
 * Но мы можем брать из него данные
 * Оригинал находиться где-то в сторонке от основной обработки....
 *
 * Это полностью противоположно ситуации с Моделями(ORM)
 * где мы используем модель данных через модель(через саму себя) здесь уже не OUTER стратегия.
 *
 * @Inner-interaction: Default
 */


/**
 * Interface OuterValueAccessAwareInterface
 * @package modelX
 */
interface OuterValueAccessAwareInterface{

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key);

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function valueAccessSet($data, $key, $value);

	/**
	 * @param $key
	 * @return bool
	 */
	public function valueAccessExists($key);

}

/**
 * Interface SchemaOuterInteractionInterface
 * @package modelX
 */
interface SchemaOuterInteractionInterface
	extends SchemaInterface, SchemaIndexedInterface, OuterValueAccessAwareInterface{

}


/**
 * Class ValueAccess
 * @package modelX
 */
class OuterValueAccess{


	/**
	 * @return OuterSetter
	 */
	public static function getDefaultSetter(){
		static $setter;
		if(!$setter){
			$setter = new OuterSetter();
		}
		return $setter;
	}

	/**
	 * @return OuterGetter
	 */
	public static function getDefaultGetter(){
		static $getter;
		if(!$getter){
			$getter = new OuterGetter();
		}
		return $getter;
	}

	/**
	 * @param $getter
	 * @return mixed
	 */
	public static function checkoutGetter($getter){
		$array = is_array($getter);
		if($getter instanceof OuterGetterInterface || $getter instanceof \Closure || $array){
			if($array){
				/** @var array $getter */
				$getter = array_replace(
					[
						'prefix'    => null,
						'method'    => null,
						'arguments' => [ ]
					],
					$getter
				);
				if(!$getter['prefix'] && !$getter['method']){
					throw new \LogicException('Error object access getter');
				}
			}
			return $getter;
		}

		throw new \LogicException('Error invalid access getter');

	}

	/**
	 * @param $setter
	 * @return mixed
	 */
	public static function checkoutSetter($setter){
		$array = is_array($setter);
		if($setter instanceof OuterGetterInterface || $setter instanceof \Closure || $array){
			if($array){
				/** @var array $setter */
				$setter = array_replace(
					[
						'prefix'                  => null,
						'method'                  => null,
						'arguments'               => [ ],
						'arguments_value_capture' => null
					],
					$setter
				);
				if(!$setter['prefix'] && !$setter['method']){
					throw new \LogicException('Error object access getter');
				}
			}
			return $setter;
		}
		throw new \LogicException('Error invalid access setter');
	}

	/**
	 * @param $getter
	 * @param $data
	 * @param $key
	 * @param array $argumentsAhead
	 * @return mixed|null
	 */
	public static function handleGetter($getter, $data, $key, array $argumentsAhead = [ ]){
		if(is_array($getter)){
			$getter = array_replace(
				[
					'prefix'    => null,
					'method'    => null,
					'arguments' => [ ]
				],
				$getter
			);
			$methodName = null;
			if($getter['prefix']){
				$methodName = $getter['prefix'] . ucfirst($key);
			}elseif($getter['method']){
				$methodName = $getter['method'];
			}
			$arguments = [ ];
			if($getter['arguments']){
				$arguments = $getter['arguments'];
			}
			if($methodName){
				return call_user_func_array([ $data, $methodName ], (array) $arguments);
			}else{
				throw new \LogicException('Object value access failure!');
			}
		}elseif(is_callable($getter)){
			$arguments = [ $data, $key ];
			if($argumentsAhead){
				$arguments = array_merge($arguments, $argumentsAhead);
			}
			return call_user_func_array($getter, $arguments);
		}else{
			return null;
		}
	}

	public static function handleSetter($setter, $data, $key, $value, array $argumentsAhead = [ ]){
		if(is_array($setter)){
			$setter = array_replace(
				[
					'prefix'                  => null,
					'method'                  => null,
					'arguments'               => [ ],
					'arguments_value_capture' => null
				],
				$setter
			);

			$methodName = null;
			if($setter['prefix']){
				$methodName = $setter['prefix'] . ucfirst($key);

			}elseif($setter['method']){
				$methodName = $setter['method'];
			}
			if($setter['arguments']){
				$arguments = $setter['arguments'];
				$valueCapture = $setter['arguments_value_capture'];

				if(is_string($valueCapture)){
					switch($valueCapture){
						case 'append':
							$arguments[] = $value;
							break;
						case 'prepend':
							array_unshift($arguments, $value);
							break;
					}
				}elseif(is_array($valueCapture)){
					if(!isset($valueCapture['type'])){
						throw new \LogicException('Value capture error array definition: invalid type parameter');
					}
					if(!isset($valueCapture['offset'])){
						throw new \LogicException('Value capture error array definition: invalid offset parameter');
					}
					$offset = $valueCapture['offset'];
					if($offset < 0 || $offset > count($arguments)){
						throw new \LogicException(
							'Value capture error array definition: invalid offset parameter not range'
						);
					}
					switch($valueCapture['type']){
						case 'insert':
							array_splice($arguments, $valueCapture['offset'], 0, [ $value ]);
							break;
						case 'replace':
							array_splice($arguments, $valueCapture['offset'], 1, [ $value ]);
							break;
					}
				}else{

				}
			}else{
				$arguments = [ $value ];
			}

			if($methodName){
				call_user_func_array([ $data, $methodName ], (array) $arguments);
				return $data;
			}else{
				throw new \LogicException('Object value access failure!');
			}
		}elseif(is_callable($setter)){
			$arguments = [ $data, $key, $value ];
			if($argumentsAhead){
				$arguments = array_merge($arguments, $argumentsAhead);
			}
			return call_user_func_array($setter, $arguments);
		}else{
			throw new \LogicException();
		}
	}


	/**
	 * @param OuterValueAccessAwareInterface|callable|null $access
	 * @param PropertyRegistryInterface|mixed $data
	 * @param string $key
	 * @return mixed
	 */
	public static function handleAccessGet($access, $data, $key){
		if($data instanceof PropertyRegistryInterface){
			$value = $data->getProperty($key);
		}elseif($access instanceof OuterValueAccessAwareInterface){
			$value = $access->valueAccessGet($data, $key);
		}elseif(is_array($access) || is_callable($access)){
			$value = self::handleGetter($access, $data, $key);
		}else{
			throw new \LogicException();
		}
		return $value;
	}

	/**
	 * @param OuterValueAccessAwareInterface|callable|null $access
	 * @param PropertyRegistryInterface|mixed $data
	 * @param string $key
	 * @param mixed $value
	 * @return PropertyRegistryInterface|mixed
	 */
	public static function handleAccessSet($access, $data, $key, $value){
		if($data instanceof PropertyRegistryInterface){
			$data->setProperty($key, $value);
		}elseif($access instanceof OuterValueAccessAwareInterface){
			$data = $access->valueAccessSet($data, $key, $value);
		}elseif(is_array($access) || is_callable($access)){
			$data = self::handleSetter($access, $data, $key, $value);
		}else{
			throw new \LogicException();
		}
		return $data;
	}

}

/**
 * Interface OuterGetterInterface
 * @package modelX
 */
interface OuterGetterInterface{

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function __invoke($data, $key);

}

/**
 * Interface OuterSetterInterface
 * @package modelX
 */
interface OuterSetterInterface{

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function __invoke($data, $key, $value);

}

/**
 * Class OuterGetter
 * @package modelX
 */
class OuterGetter implements OuterGetterInterface{

	/**
	 * @param $data
	 * @param $key
	 * @return mixed|null
	 */
	public function __invoke($data, $key){
		if($data === null){
			return null;
		}
		if($data instanceof PropertyRegistryInterface){
			return $data->getProperty($key);
		}elseif(is_array($data) || $data instanceof \ArrayAccess){
			return $data[$key];
		}elseif(is_object($data)){
			return $data->{$key};
		}else{
			throw new \LogicException('[OuterGetter] Wrong data type');
		}
	}

}


/**
 * Class OuterSetter
 * @package modelX
 */
class OuterSetter implements OuterSetterInterface{

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return array|\ArrayAccess
	 */
	public function __invoke($data, $key, $value){
		if($data === null){
			return [$key => $value];
		}
		if($data instanceof PropertyRegistryInterface){
			$data->setProperty($key, $value);
		}elseif(is_array($data) || $data instanceof \ArrayAccess){
			$data[$key] = $value;
		}elseif(is_object($data)){
			$data->{$key} = $value;
		}else{
			throw new \LogicException('[OuterSetter] Wrong data type');
		}
		return $data;
	}

}


/**
 * Interface OriginalFieldNameAware
 * @package modelX
 */
interface OriginalFieldNameAware{

	/**
	 * @param $key
	 * @return mixed
	 */
	public function setOriginalKey($key);

	/**
	 * @return mixed
	 */
	public function getOriginalKey();

}

/**
 * Interface SetterAwareInterface
 * @package modelX
 */
interface SetterAwareInterface{

	/**
	 * @param $setter
	 * @return mixed
	 */
	public function setSetter($setter);

	/**
	 * @return mixed
	 */
	public function getSetter();

}

/**
 * Interface GetterAwareInterface
 * @package modelX
 */
interface GetterAwareInterface{

	/**
	 * @param $setter
	 * @return mixed
	 */
	public function setGetter($setter);

	/**
	 * @return mixed
	 */
	public function getGetter();

}
/**
 * Interface RecordSchemaInterface
 * @package modelX
 */
interface RecordSchemaInterface{

	/**
	 * @return string
	 */
	public function getSource();

	/**
	 * @return mixed
	 * db, fs, api , etc...
	 */
	public function getSourceType();

	/**
	 * @return array
	 */
	public function getSourceFieldNames();

	/**
	 * @return string
	 */
	public function getSourcePrimaryName();

}


/**
 * Interface FormulaAwareInterface
 * @package modelX
 *
 * Получение значений из текущей схемы
 *
 *
 * Схема будет выступать с набором полей маппинга оригинальных ключей:
 * [id]     -  [db_id]
 * [name]   -  [db_name]
 *
 * В этом случае пользовательские ValueAccess недопустимы.
 *
 * ValueAccess в схеме носят исключительно характер доступа к типу Всего объекта данных array|object||||etc
 * Именно по-этому у ValueAccess есть параметр $key - он является задающим для локации по $data
 *
 * Есть магия в способе определения ключа он может быть просто названием поля,
 * а может быть и оригинальным ключем [db_id]
 *
 * Типы внешнего взаимодействия по $key при Getter/Setter:
 *      Использование Псевдонима поля схемы
 *      Использования Имени поля схемы
 *
 *
 */
interface FormulaAwareInterface{

	/**
	 * @param $formula_set
	 * @return mixed
	 */
	public function setFormulaSetter($formula_set);

	/**
	 * @return mixed
	 */
	public function getFormulaSetter();

	/**
	 * @param $formula_getter
	 * @return mixed
	 */
	public function setFormulaGetter($formula_getter);

	/**
	 * @return mixed
	 */
	public function getFormulaGetter();

}

/**
 *
 *
 *
 * Interface CustomOuterInteractionInterface
 * @package modelX
 *
 * Пользовательские Getter\Setter
 *
 *
 */
interface CustomOuterInteractionAwareInterface{

	/**
	 * @param $setter
	 * @return $this
	 */
	public function setSetter($setter);

	/**
	 * @return mixed
	 */
	public function getSetter();


	/**
	 * @param $getter
	 * @return $this
	 */
	public function setGetter($getter);

	/**
	 * @return mixed
	 */
	public function getGetter();

}





/**
 * @Outer-Interaction
 * Class SchemaOuterInteraction
 * @package modelX
 *
 * @property FieldOuterInteraction[]    $fields
 *
 * @method FieldOuterInteraction        getField($key)
 * @method FieldOuterInteraction[]      getFields()
 * @method FieldOuterInteraction        getPrimaryField()
 */
abstract class SchemaOuterInteraction
	extends Schema
	implements SchemaInterface,
	SchemaIndexedInterface,
	OuterValueAccessAwareInterface{

	protected function beforeAddField($field){
		if(!$field instanceof FieldOuterInteraction){
			throw new \LogicException();
		}
	}

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key){
		$field = $this->getField($key);
		if($field){
			return $field->valueAccessGet($data, $key);
		}else{
			throw new \LogicException('Field "' . $key . '" not found');
		}
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function valueAccessSet($data, $key, $value){
		$field = $this->getField($key);
		if($field){
			return $field->valueAccessSet($data, $key, $value);
		}else{
			throw new \LogicException('Field "' . $key . '" not found');
		}
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessExists($key){
		return !!$this->getField($key);
	}


}

/**
 * Class SchemaMappedOuterInteraction
 * @package modelX
 *
 * @property FieldMappedOuterInteraction[]    $fields
 *
 * @method FieldMappedOuterInteraction        getField($key)
 * @method FieldMappedOuterInteraction[]      getFields()
 * @method FieldMappedOuterInteraction        getPrimaryField()
 *
 */
abstract class SchemaMappedOuterInteraction extends SchemaOuterInteraction{


	/**
	 * @param $field
	 */
	protected function beforeAddField($field){
		if(!$field instanceof FieldMappedOuterInteraction){
			throw new \LogicException();
		}
	}

	/**
	 * @return string[]
	 */
	public function getOriginalNames(){
		$names = [];
		foreach($this->fields as $field){
			if($field instanceof FieldMappedOuterInteraction){
				$names[] = $field->getOriginalKey();
			}
		}
		return $names;
	}

	/**
	 * @return string
	 */
	public function getOriginalPrimaryName(){
		return $this->getPrimaryField()->getOriginalKey();
	}

}

/**
 * Class FieldOuterInteraction
 * @package modelX
 *
 * Поле знающее как получить данные из внешнего объекта данных array|object|...etc
 *
 * @method SchemaOuterInteraction getSchema()
 */
abstract class FieldOuterInteraction extends Field implements OuterValueAccessAwareInterface{

	/**
	 * @return array|callable
	 */
	public function getSetter(){
		return OuterValueAccess::getDefaultSetter();
	}

	/**
	 * @return array|callable
	 */
	public function getGetter(){
		return OuterValueAccess::getDefaultGetter();
	}

	/**
	 * @param null $key
	 * @return string
	 */
	protected function _getOuterInteractionKey($key = null){
		return $this->name;
	}

	/**
	 * @param $data
	 * @param $key
	 * @return mixed|null
	 */
	public function valueAccessGet($data, $key){
		return OuterValueAccess::handleGetter($this->getGetter(),$data,$this->_getOuterInteractionKey(),[$this]);
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function valueAccessSet($data, $key, $value){
		return OuterValueAccess::handleSetter($this->getSetter(), $data, $this->_getOuterInteractionKey($key), $value,[$this]);
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessExists($key){
		return $this->name === $this->_getOuterInteractionKey($key);
	}


}

/**
 * Class FieldMappedOuterInteraction
 * @package modelX
 *
 * Получение данных с использованием оригинального ключа
 *
 */
abstract class FieldMappedOuterInteraction extends FieldOuterInteraction{

	/** @var string|null */
	protected $original_key;

	/**
	 * @param $key
	 * @return $this
	 */
	public function setOriginalKey($key){
		$this->original_key = $key;
		return $this;
	}


	/**
	 * @return int|null|string
	 */
	public function getOriginalKey(){
		return $this->original_key;
	}

	/**
	 * @param null $key
	 * @return string
	 */
	protected function _getOuterInteractionKey($key = null){
		if($this->original_key){
			return $this->original_key;
		}
		return parent::_getOuterInteractionKey($key);
	}


}

/**
 * Class FieldCustomOriginalOuterInteraction
 * @package modelX
 *
 * Кастомное получение данных из оригинала посредством пользовательской функции Getter|Setter
 */
abstract class FieldCustomOuterInteraction extends FieldMappedOuterInteraction{

	/** @var  callable|array|null */
	protected $setter;

	/** @var  callable|array|null */
	protected $getter;

	/**
	 * @param $setter
	 * @return $this
	 */
	public function setSetter($setter){
		$this->setter = OuterValueAccess::checkoutSetter($setter);
		return $this;
	}

	/**
	 * @param $getter
	 * @return $this
	 */
	public function setGetter($getter){
		$this->getter = OuterValueAccess::checkoutGetter($getter);
		return $this;
	}

	/**
	 * @return OuterGetterInterface|array|callable
	 */
	public function getSetter(){
		if($this->setter){
			return $this->setter;
		}
		return parent::getSetter();
	}

	/**
	 * @return OuterGetterInterface|array|callable
	 */
	public function getGetter(){
		if($this->getter){
			return $this->getter;
		}
		return parent::getGetter();
	}

}



/**
 * Class ValueAccessor
 * @package modelX
 */
class ValueAccessor{

	/** @var  mixed */
	protected $data;

	/** @var  OuterValueAccessAwareInterface */
	protected $accessor;

	/**
	 * @param OuterValueAccessAwareInterface $accessor
	 * @return $this
	 */
	public function setAccessor(OuterValueAccessAwareInterface $accessor){
		$this->accessor = $accessor;
		return $this;
	}

	/**
	 * @return OuterValueAccessAwareInterface
	 */
	public function getAccessor(){
		return $this->accessor;
	}

	/**
	 * @param $data
	 * @return $this
	 */
	public function setData($data){
		$this->data = $data;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData(){
		return $this->data;
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public function __get($name){
		return $this->accessor->valueAccessGet($this->data, $name);
	}

	/**
	 * @param $name
	 * @param $value
	 * @return mixed
	 */
	public function __set($name, $value){
		$this->data = $this->accessor->valueAccessSet($this->data, $name, $value);
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function __isset($name){
		return $this->accessor->valueAccessExists($name);
	}

}



/** -----------------------------------------------------------
 * -------------   @DataMap(Outer-Interaction)   --------------
 * -----------------------------------------------------------*/


/**
 * Interface SchemaAwareInterface
 * @package modelX
 */
interface SchemaAwareInterface{

	/**
	 * @return mixed
	 */
	public function getSchema();

}

/**
 * Interface OriginalDataAwareInterface
 * @package modelX
 */
interface OriginalDataAwareInterface{

	/**
	 * @param $original_data
	 * @return mixed
	 */
	public function setOriginalData($original_data);

	/**
	 * @return mixed
	 */
	public function getOriginalData();

}

/**
 * Interface OriginalDataTransientInterface
 * @package modelX
 */
interface OriginalDataTransientInterface{

	/**
	 * @return bool
	 */
	public function hasModifiedOriginalData();

	/**
	 * @return mixed
	 */
	public function getModifiedOriginalData();

	/**
	 * @return $this
	 */
	public function applyModifiedOriginalData();

}


/**---------------------------------------------------------
 * -------------   @Property   --------------------------
 * ------------------------------------------------------*/


/**
 * Interface PropertyRegistryInterface
 * @package modelX
 */
interface PropertyRegistryInterface{

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function setProperty($key, $value);

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasProperty($key);

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getProperty($key);

}

/**
 * Interface PropertyRegistryRemovableInterface
 * @package modelX
 */
interface PropertyRegistryRemovableInterface{

	/**
	 * @param $key
	 * @return mixed
	 */
	public function removeProperty($key);

}

/**
 * Interface PropertyRegistryTransientInterface
 * @package modelX
 */
interface PropertyRegistryTransientInterface{

	/**
	 * @param null $field
	 * @return bool
	 */
	public function hasChangesProperty($field = null);

	/**
	 * @return string[]
	 */
	public function getChangedProperties();

}


/** --------------------------------------------------------------------
 * ----------   @Registry   -----------------------------------------------
 * -------------------------------------------------------------------------------
 */

/**
 * Interface RegistryInterface
 * @package modelX
 */
interface RegistryReadInterface{

	/**
	 * @param $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * @param $key
	 * @return mixed
	 */
	public function has($key);

}


/**
 * Interface RegistryWriteInterface
 * @package modelX
 */
interface RegistryWriteInterface{

	/**
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function set($key, $value);

}

/**
 * Interface RegistryRemovableInterface
 * @package modelX
 */
interface RegistryRemovableInterface{

	/**
	 * @param $key
	 * @return mixed
	 */
	public function remove($key);

}

/**
 * Interface RegistryInterface
 * @package modelX
 */
interface RegistryInterface extends RegistryWriteInterface, RegistryReadInterface{}





/**
 * Interface ExportableDataAwareInterface
 * @package modelX
 */
interface ExportableDataAwareInterface{

	/**
	 * @return array
	 */
	public function export();

}

/**
 * Interface ImmutableSwitchingInterface
 * @package modelX
 */
interface ImmutableSwitchingInterface{

	/**
	 * @param bool|true $immutable
	 * @param $anxiety
	 * @return $this
	 */
	public function setImmutable($immutable = true, $anxiety = false);

	/**
	 * @return bool
	 */
	public function isImmutable();

}




/** -----------------------------------------------------------
 * --------------   @Validators   ------------------------------
 * -------------------------------------------------------------------*/


/**
 * Interface ValidatorInterface
 * @package modelX
 */
interface ValidatorInterface{

	public function validate($value);

}

interface ValidatorRuleInterface{

	public function getFieldName();

}

interface ValueTypeInterface{

	public function getName();

}


interface FrontValueTypeInterface{

	public function getName();

}

interface BackValueTypeInterface{

	public function getName();

}

/** ----------------------------------------------------------------
 * ------------------   @Transient-State   ----------------------------
 * ----------------------------------------------------------------------*/


/**
 * Class TransientState
 * @package modelX
 */
class TransientState{

	/** @var  TransientState */
	protected $previous;

	/** @var  array */
	protected $data;

	/** @var bool */
	protected $fixed = false;


	/**
	 * State constructor.
	 * @param $data
	 * @param TransientState|null $previous
	 */
	public function __construct(array $data, TransientState $previous = null){
		$this->data = $data;
		$this->previous = $previous;
	}

	/**
	 * @return TransientState
	 */
	public function getPrevious(){
		return $this->previous;
	}

	/**
	 * @return bool
	 */
	public function isInitial(){
		return $this->fixed && !$this->previous;
	}

	/**
	 * @return bool
	 */
	public function isFixed(){
		return $this->fixed;
	}

	/**
	 * @param bool|true $fixed
	 * @return $this
	 */
	public function setFixed($fixed = true){
		$this->fixed = $fixed;
		return $this;
	}

	/**
	 * @param array $instant Last fixed data
	 * @return array
	 */
	public function getData(array $instant = null){
		if($this->previous && !$this->previous->isFixed()){
			$data = $this->previous->getData();
		}else{
			return $this->data;
		}
		$data = array_replace($data, $this->data);
		if(is_array($instant)){
			foreach($instant as $property => $instantValue){
				if(!array_key_exists($property, $data)){
					$data[$property] = $instantValue;
				}
			}
		}
		return $data;
	}

}





/**
 * Class RecordField
 * @package modelX
 * @method RecordSchema        getSchema()
 *
 */
abstract class RecordField extends FieldMappedOuterInteraction implements FieldVisibilityControlInterface{

	/** @var  bool  */
	protected $readonly = false;

	/** @var  bool  */
	protected $private = false;

	/**
	 * @var null
	 */
	protected $vartype = null;

	/**
	 * @return bool
	 */
	public function isReadonly(){
		return $this->readonly;
	}

	/**
	 * @return bool
	 */
	public function isPrivate(){
		return $this->private;
	}


	/**
	 * @return string
	 */
	public function getOriginalKey(){
		return $this->original_key?:$this->name;
	}

	/**
	 * @param $value
	 * @param Record $record
	 */
	public function validate($value, Record $record = null){

	}

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key){
		$value = parent::valueAccessGet($data, $key);
		if($value === null){
			$value = $this->getDefault();
		}
		if($value && $this->vartype){
			settype($value, $this->vartype);
		}
		return $value;
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed|null
	 */
	public function valueAccessSet($data, $key, $value){
		return parent::valueAccessGet($data, $key);
	}

}

/**
 * Class VirtualRecordField
 * @package modelX
 */
abstract class VirtualRecordField extends RecordField{

	/** @var  RecordSchema */
	protected $schema;

}

/**
 * Class FormulaRecordField
 * @package modelX
 *
 */
abstract class FormulaRecordField extends VirtualRecordField{



	protected $formula_key;


	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key){
		if($this->formula_key){
			$value = $this->schema->valueAccessGet($data, $this->formula_key);
		}else{
			return parent::valueAccessGet($data,$key);
		}
		return $value;
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed|null
	 */
	public function valueAccessSet($data, $key, $value){
		if($this->formula_key){
			return $this->schema->valueAccessSet($data, $this->formula_key, $value);
		}else{
			return parent::valueAccessGet($data,$key);
		}
	}

}

/**
 * Class FormulaCustomField
 * @package modelX
 */
abstract class FormulaCustomField extends FormulaRecordField{

	protected $formula_getter;

	protected $formula_setter;

}

/**
 * Class RelationRecordField
 * @package modelX
 */
abstract class RelationRecordField extends VirtualRecordField
{

	const TYPE_BELONGS = 1;

	const TYPE_ONE = 3;

	const TYPE_MANY = 2;

	const TYPE_INTERMEDIATE = 3;

	protected $relation_type;

	/** @var  string */
	protected $reference_schema;

	/** @var  array  */
	protected $reference_fields = [];

	/** @var  array  */
	protected $self_fields = [];

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key){
		$condition = [];
		foreach($this->self_fields as $i => $name){
			$ref_name = $this->reference_fields[$i];
			$value = $this->schema->valueAccessGet($data, $name);
			$condition[] = [$ref_name, '=', $value];
		}
		$schema = $this->schema->getSchemaManager()->getSchema($this->reference_schema);
		$results = $schema->load($condition);
		return $results;
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function valueAccessSet($data, $key , $value){}


}

/**
 * Class RelationBelongsRecordField
 * @package modelX
 */
abstract class RelationBelongsRecordField extends RelationRecordField{

	protected $reference_schema = 'User';

	protected $reference_fields = ['profile_id'];

	protected $identifier_fields = ['id'];

}

/**
 * Class RelationOneRecordField
 * @package modelX
 */
abstract class RelationOneRecordField extends RelationRecordField{

	protected $reference_schema = 'Profile';

	protected $reference_fields = ['profile_id'];

	protected $identifier_fields = ['id'];

}

/**
 * Class RelationManyRecordField
 * @package modelX
 */
abstract class RelationManyRecordField extends RelationRecordField{

	protected $reference_schema = 'Message';

	protected $reference_fields = ['user_id'];

	protected $identifier_fields = ['id'];

}

/**
 * Class RelationIntermediateRecordField
 * @package modelX
 */
abstract class RelationIntermediateRecordField extends RelationRecordField{

	protected $from_fields = ['id'];

	protected $intermediate_schema = 'UserGroupMember';
	protected $intermediate_from_fields = ['user_id'];
	protected $intermediate_to_fields = ['user_group_id'];

	protected $to_schema = 'UserGroup';
	protected $to_fields = ['id'];

}

/**
 * Class Relation
 * @package modelX
 */
abstract class Relation{

	protected $left_schema;

	protected $left_fields = [];

	protected $right_schema;

	protected $right_fields = [];

}


/**
 * Class IntermediateRelation
 * @package modelX
 */
abstract class IntermediateRelation{

	protected $left_schema;

	protected $left_fields = [];


	protected $intermediate_schema;

	protected $intermediate_left_fields = [];

	protected $intermediate_right_fields = [];


	protected $right_schema;

	protected $right_fields = [];

}



/**
 * Class RecordSchema
 * @package modelX
 *
 * @property RecordField[]    $fields
 * @method RecordField        getField($key)
 * @method RecordField[]      getFields()
 * @method RecordField        getPrimaryField()
 *
 */
abstract class RecordSchema extends SchemaMappedOuterInteraction{

	/** @var  string */
	protected $name;

	/** @var  string */
	protected $default_source;

	/** @var  SourceManager */
	protected $default_source_manager;

	/** @var  SchemaManager */
	protected $schema_manager;

	/**
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @param $field
	 */
	protected function beforeAddField($field){
		if(!$field instanceof RecordField){
			throw new \LogicException();
		}
	}

	/**
	 * @param string $source
	 * @return $this
	 */
	public function setDefaultSource($source){
		$this->default_source = $source;
		return $this;
	}

	/**
	 * @param Record $record
	 * @return string
	 */
	public function getSource(Record $record = null){
		if($record && ($source = $record->getSource())){
			return $source;
		}
		return $this->default_source;
	}


	/**
	 * @param SourceManager $sourceManager
	 * @return $this
	 */
	public function setDefaultSourceManager($sourceManager){
		$this->default_source_manager = $sourceManager;
		return $this;
	}

	/**
	 * @param Record|null $record
	 * @return SourceManager
	 */
	public function getReadSourceManager(Record $record = null){
		if($record && ($source = $record->getReadSourceManager())){
			return $source;
		}
		return $this->default_source_manager;
	}

	/**
	 * @param Record|null $record
	 * @return SourceManager
	 */
	public function getWriteSourceManager(Record $record = null){
		if($record && ($source = $record->getWriteSourceManager())){
			return $source;
		}
		return $this->default_source_manager;
	}


	/**
	 * @param SchemaManager $schemaManager
	 * @return $this
	 */
	public function setSchemaManager($schemaManager){
		$this->schema_manager = $schemaManager;
		return $this;
	}

	/**
	 * @return SchemaManager
	 */
	public function getSchemaManager(){
		return $this->schema_manager;
	}

	/**
	 * @param $orderBy
	 * @return mixed
	 */
	public function normalizeOrder($orderBy){

	}

	/**
	 * @param $condition
	 * @return mixed
	 */
	public function normalizeCondition($condition){
		foreach($condition as & $cond){
			if(is_array($cond) && count($cond)>1){
				$cond[0] = $this->getField($cond[0])->getOriginalKey();
			}
		}
		return $condition;
	}

	/**
	 * @param $condition
	 * @param null $limit
	 * @param null $offset
	 * @param null $orderBy
	 * @param array $options
	 * @return mixed
	 */
	public function load($condition, $limit = null, $offset = null, $orderBy = null,array $options = null){
		if($condition){
			$condition = $this->normalizeCondition($condition);
		}
		if($orderBy){
			$orderBy = $this->normalizeOrder($orderBy);
		}
		$columns = [];
		foreach($this->getFields() as $field){
			if(!$field instanceof VirtualRecordField){
				$columns[] = $field->getOriginalKey();
			}
		}
		$manager = $this->getReadSourceManager();
		return $manager->select($columns,$this->getSource(), $condition, $limit, $offset, $orderBy,$options);
	}

	/**
	 * @param $id
	 * @return null
	 */
	public function loadById($id){
		$condition = $this->normalizeCondition([$this->getPrimaryField()->getName(),'=',$id]);
		$map = [];
		foreach($this->getFields() as $field){
			$map[] = $field->getOriginalKey();
		}
		$manager = $this->getReadSourceManager();
		$results = $manager->select($map,$this->getSource(), $condition, 1);
		if($results){
			return $this->initializeRecord($results[0]);
		}else{
			return null;
		}
	}

	protected function initializeRecord($entity){
		return null;
	}


}




/**
 * Class SchemaManager
 * @package modelX
 */
abstract class SchemaManager{

	/** @var  ModelCollection[] */
	protected $collections = [];

	/** @var  ModelSchema[] */
	protected $schemas = [];

	/**
	 * @param $schemaName
	 * @return bool
	 */
	public function isRecognized($schemaName){
		return isset($this->schemas[$schemaName]);
	}


	public function getCollection($schemaName){

		if(!isset($this->collections[$schemaName])){




		}

		return $this->collections[$schemaName];
	}


	/**
	 * @param $schemaName
	 * @return RecordSchema
	 */
	public function getSchema($schemaName){
		if(!isset($this->schemas[$schemaName])){
			$this->schemas[$schemaName] = $this->loadSchema($schemaName);
		}
		return $this->schemas[$schemaName];
	}

	/**
	 * @param $schemaName
	 * @return SchemaOuterInteraction
	 *
	 * Вот именно здесь мы и ищем и создаем схему по её установленному и полученому определению
	 *
	 */
	protected function loadSchema($schemaName){
		if(!class_exists($schemaName)){
			throw new \LogicException('Schema class "'.$schemaName.'" not found!');
		}
		$definition = $schemaName::getSchemaDefinition();
		return buildDefinition($definition);
	}

	/**
	 * @param SchemaOuterInteraction $schema
	 *
	 * По загруженой схеме инициализируется коллекция для будущих загруженных объектов
	 *
	 *
	 */
	protected function initCollection(SchemaOuterInteraction $schema){

	}


	public function updateRecord(Record $record){

	}

	public function createRecord(Record $record){

	}

	public function removeRecord(Record $record){

	}

}

/**
 * Class SourceManager
 * @package modelX
 *
 * map conversion [sys] => [origin]
 * Select:
 * ordering
 * grouping
 * column list
 *
 * Схемой можно ограничить предоставляемые поля
 *  Допустим у схемы есть поля `id`, `name`, `city`, `created_at`
 *
 *  Мы хотим чтобы выборка по схеме происходила только для полей `id`, `name`, `city` исключив поле `created_at`
 *  Такие схемы могут производить полую выборку по оригиналу,
 * а могут производить умную выборку - но тогда где-то в коллекции будет урезаная запись по такой схеме.
 * При этом при попытке доступа уже к оригинальной схеме произойдет выбор решения:
 * Либо использовать уже загруженую запись по урезаной схеме что требует актуализировать недостоющие поля,
 * Либо использовать новый запрос для обновления такой
 *
 * Схему можно дополнить какими-то полями:
 *
 * Создав виртуальные поля, такие поля требуют Getter\Setter доступа к оригиналу данных,
 * или же использовав доступ к текущей схеме (FormulaGetter\FormulaSetter),
 * где мы получим данные соответствующие реальным типам переменных.
 *
 * Поля связей по внешним ключам тоже можно прировнять к виртуальным полям с приставкой Relation, НО
 * объекты по связям нужно использовать в стиле LazyLoad с использованием соответствующих коллекций и связаных схем.
 *
 * В случае если мы хотим добавить в текущую схему допустим поле `password`, которого в принципе нету в оригинальной схеме
 * тогда встает вопрос о выборке этого поля из другой схемы в которой есть поля этой семантики.
 *      Выбрать данные такого поля сопоставив записи как-то по ключам!
 *      Получить доступ к записям внешней схемы, точнее к источнику этих записей для постройки запроса,
 *          Или найти запись по идентификатору в инициированых объектах-записях отобрав нужный.
 * Таким образом вполне реально создать условия для сливания множества схем в единую посредством примешивания,
 *
 * Или же в таблице присутствует такое поле, просто его не добавили в главную схему, тогда мы просто добавляем поле в новой схеме
 *
 * @TODO Schema Extending Descendant\Ancestor
 *
 *
 * Прослойка между реальным источником и модельной фасадом,
 * как видим здесь производятся действия на нижнем уровне взаимодействия с БД
 *
 *
 * @TODO Transfer model field meta data   to   original source field meta data   before use low-level-processing
 *
 */
abstract class SourceManager implements StorageInterface{





}

class DataBaseSourceManager extends SourceManager{

	/** @var \Jungle\Data\Storage\Db\Adapter */
	protected $adapter;

	/**
	 * @param $adapter
	 * @return $this
	 */
	public function setAdapter($adapter){
		$this->adapter = $adapter;
		return $this;
	}


	/**
	 * @param $data
	 * @param $source
	 * @return mixed
	 */
	public function create($data,$source = null){
		if($this->adapter->insert($source, array_keys($data), array_values($data))===1){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param $condition
	 * @param $data
	 * @param null $source
	 * @return mixed
	 */
	public function update($data,$condition, $source){
		if($this->adapter->update($source, array_keys($data), array_values($data),null,[
				'condition' => $condition, 'extra' => true
			])!==false){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param $condition
	 * @param $data
	 * @param null $source
	 * @return bool
	 */
	public function updateCollection($condition, $data, $source = null){
		if($this->adapter->update($source, array_keys($data), array_values($data),null,[
				'condition' => $condition, 'extra' => true
			])!==false){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param $condition
	 * @param null $source
	 * @return bool
	 */
	public function delete($condition, $source = null){
		if($this->adapter->delete($source, [ 'condition' => $condition, 'extra' => true ])!==false){
			return true;
		}else{
			return false;
		}
	}


	/**
	 * @param  $columns
	 * @param  $source
	 * @param $condition
	 * @param null $limit
	 * @param null $offset
	 * @param null $orderBy
	 * @return mixed
	 */
	public function select($columns, $source,  $condition, $limit = null, $offset = null, $orderBy = null,array $options = null){
		$query = [
			'table' => $source,
			'columns' => $columns,
			'limit' => $limit,
			'offset' => $offset,
			'order' => $orderBy
		];
		if($condition) {
			$query['where'] = ['condition' => $condition,'extra' => true];
		}
		if($options){
			$query = array_replace($query,$options);
		}
		$results = $this->adapter->fetchAll($query);
		return $results;
	}



}



/**
 * Class Record
 * @package modelX
 */
abstract class Record
	implements  PropertyRegistryInterface,
				PropertyRegistryTransientInterface,
				ExportableDataAwareInterface,
				\Iterator,
				\ArrayAccess,
				\Serializable,
				\JsonSerializable{

	const OP_NONE = 0;

	const OP_CREATE = 1;

	const OP_UPDATE = 2;

	const OP_DELETE = 3;





	/** @var  RecordSchema */
	protected $_schema;

	/** @var  int  */
	protected $_operation_made = self::OP_CREATE;

	/** @var  array  */
	protected $_property_names = [];

	/** @var  int */
	private   $_property_iterator_index = 0;

	/** @var  int */
	private   $_property_iterator_count = 0;

	/**
	 * Record constructor.
	 */
	public function __construct(){}


	/**
	 * @param RecordSchema $schema
	 * @return $this
	 */
	public function setSchema(RecordSchema $schema){
		$this->_schema = $schema;
		$this->_property_names = $schema->getFieldNames();
		return $this;
	}

	/**
	 * @return RecordSchema
	 */
	public function getSchema(){
		return $this->_schema;
	}


	/**
	 * @return string
	 */
	public function getSource(){
		return $this->_schema->getSource();
	}

	/**
	 * @return SourceManager|string
	 */
	public function getReadSourceManager(){
		return $this->_schema->getReadSourceManager();
	}

	/**
	 * @return SourceManager|string
	 */
	public function getWriteSourceManager(){
		return $this->_schema->getWriteSourceManager();
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public function __get($name){
		return $this->getProperty($name);
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value){
		$this->setProperty($name, $value);
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function __isset($name){
		return $this->hasProperty($name);
	}

	/**
	 * @param $name
	 */
	public function __unset($name){
		$this->resetProperty($name);
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public function resetProperty($name){
		$field = $this->_schema->getField($name);
		$default = $field->getDefault();
		if(($default!==null) || ($default === null && $field->isNullable())){
			return $this->setProperty($name, $default);
		}else{
			throw new \LogicException('Property "'.$name.'" not have default value');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists($offset){
		return $this->hasProperty($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet($offset){
		return $this->getProperty($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet($offset, $value){
		$this->setProperty($offset, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset($offset){
		$this->resetProperty($offset);
	}


	/**
	 * @return mixed
	 */
	public function current(){
		return $this->getProperty($this->_property_names[$this->_property_iterator_index]);
	}

	/**
	 * @inheritDoc
	 */
	public function next(){
		$this->_property_iterator_index++;
	}

	/**
	 * @return string
	 */
	public function key(){
		return $this->_property_names[$this->_property_iterator_index];
	}

	/**
	 * @return bool
	 */
	public function valid(){
		return $this->_property_iterator_index < $this->_property_iterator_count;
	}

	/**
	 * @inheritDoc
	 */
	public function rewind(){
		$this->_property_iterator_index = 0;
		$this->_property_iterator_count = count($this->_property_names);
	}

	/**
	 * @return array
	 */
	public function export(){
		$values = [];
		foreach($this->_property_names as $name){
			$values[$name] = $this->getProperty($name);
		}
		return $values;
	}

	/**
	 *
	 */
	public function refresh(){

	}

	/**
	 *
	 */
	public function reset(){

	}

	/**
	 * @return string
	 */
	public function serialize(){
		return serialize($this->export());
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized){
		$serialized = unserialize($serialized);

	}

	public function jsonSerialize(){
		return $this->export();
	}


	/**
	 * @param $name
	 * @return Record
	 */
	public function getRelated($name){

	}

	/**
	 * @param $name
	 * @param null $limit
	 * @param null $offset
	 * @param null $condition
	 * @return Record[]
	 */
	public function getRelatedCollection($name, $limit = null, $offset = null, $condition = null){

	}


	/**
	 * @return bool
	 */
	public function save(){
		switch($this->_operation_made){
			case self::OP_CREATE:
				if($this->beforeSave()!==false && $this->beforeCreate()!==false){
					$this->_operation_made = self::OP_NONE;
					if($this->_doCreate()){
						$this->_operation_made = self::OP_UPDATE;
						$this->onCreate();
						$this->onSave();
						return true;
					}
				}
				break;

			case self::OP_UPDATE:
				$changed = $this->getChangedProperties();
				if(!$changed){
					return true;
				}
				if($this->beforeSave()!==false && $this->beforeUpdate()!==false){
					$this->_operation_made = self::OP_NONE;
					if($this->_doUpdate($changed)){
						$this->_operation_made = self::OP_UPDATE;
						$this->onUpdate();
						$this->onSave();
						return true;
					}
				}
				break;
			case self::OP_DELETE:
			case self::OP_NONE:
				return true;
				break;
		}
		return false;
	}


	/**
	 * @return bool
	 */
	public function remove(){
		if($this->_operation_made === self::OP_UPDATE && ($this->beforeRemove()!==false)){
			if(!$this->_doRemove()){
				return false;
			}
		}
		$this->_operation_made = self::OP_DELETE;
		return true;
	}

	/**
	 * @param $changed
	 * @return bool
	 */
	protected function _doUpdate($changed){
		$dynamicUpdate = true;

		$data = [];
		$primary = null;
		foreach($this->_schema->getFields() as $field){
			if(!$primary && $field->isPrimary()){
				$primary = $field;
			}
			$name = $field->getName();
			if(!$dynamicUpdate || in_array($name,$changed,true)){
				$data[$field->getOriginalKey()] = $this->getProperty($name);
			}
		}

		if($primary){
			$identifier = $this->getProperty($primary->getName());
			$source = $this->getSource();
			$primaryOriginal = $primary->getOriginalKey();
			$sourceManager = $this->getWriteSourceManager();
			if($sourceManager->update($data,[[$primaryOriginal,'=',$identifier]], $source)){
				return true;
			}
		}else{
			throw new \LogicException('Record schema not have primary field!');
		}
		return false;
	}

	/**
	 * @return bool
	 */
	protected function _doCreate(){
		$sourceManager = $this->getWriteSourceManager();
		$data = [];
		foreach($this->_schema->getFields() as $field){
			if(/* field is virtual*/false){
				if(/* field is relation*/false){
					// handle dirty relation records before self create
				}else{}
			}else{
				$data[$field->getOriginalKey()] = $this->getProperty($field->getName());
			}
		}
		return $sourceManager->create($data, $this->getSource());
	}

	/**
	 * @return bool
	 */
	protected function _doRemove(){
		$sourceManager = $this->getWriteSourceManager();
		$idField = $this->_schema->getPrimaryField();
		$idKey = $idField->getOriginalKey();
		$id = $idField->pack($this->getProperty($idField->getName()));
		return $sourceManager->delete([$idKey,'=',$id],$this->getSource());
	}


	protected function beforeSave(){}

	protected function onSave(){}

	protected function beforeCreate(){}

	protected function onCreate(){}

	protected function beforeUpdate(){}

	protected function onUpdate(){}

	protected function beforeRemove(){}

	protected function onRemove(){}

	protected function afterFetch(){}



}



/**
 * Class DataMap - Предвестник ORM ~ Моделей
 * @package modelX
 */
class DataMap extends Record{

	/** @var   */
	protected $_initial;

	/** @var array */
	protected $_processed = [];

	/** @var array  */
	protected $_properties = [];

	/**
	 * DataMap constructor.
	 * @param RecordSchema $schema
	 * @param null $data
	 */
	public function __construct(RecordSchema $schema, $data = null){
		$this->setSchema($schema);
		if($data!==null){
			$this->_operation_made = self::OP_UPDATE;
			$this->_initial = $data;
		}else{
			$this->_operation_made = self::OP_CREATE;
		}
	}

	/**
	 * @param RecordSchema $schema
	 * @param $condition
	 * @param null $limit
	 * @param null $offset
	 * @param null $orderBy
	 * @param array $options
	 * @return DataMap[]
	 */
	public static function loadCollection(RecordSchema $schema, $condition, $limit = null, $offset = null, $orderBy = null, array $options = null){
		$records = [];
		if(!$options)$options = [];
		$options['for_update'] = true;
		//$options['lock_in_shared'] = true;
		foreach(new \ArrayIterator($schema->load($condition,$limit,$offset,$orderBy,$options)) as $record){
			$records[] = new DataMap($schema, $record);
		}
		return $records;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function setProperty($key, $value){
		if(in_array($key, $this->_property_names, true)){
			$this->_properties[$key] = $value;
		}
		return $this;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasProperty($key){
		return in_array($key, $this->_property_names, true);
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getProperty($key){
		if(in_array($key, $this->_property_names, true)){
			if(!array_key_exists($key, $this->_properties)){
				$this->_properties[$key] = $value = $this->_getProcessed($key);
				return $value;
			}
			return $this->_properties[$key];
		}else{
			throw new \LogicException('Field "'.$key.'" not exists in data map schema');
		}
	}


	/**
	 * @param $key
	 * @return mixed
	 */
	protected function _getProcessed($key){
		if(!array_key_exists($key, $this->_processed)){
			if(!$this->_initial){
				$this->_processed[$key] = $processed = $this->_schema->getField($key)->getDefault();
			}else{
				$this->_processed[$key] = $processed = $this->_schema->valueAccessGet($this->_initial, $key);
			}
			return $processed;
		}
		return $this->_processed[$key];
	}

	/**
	 * @param null $field
	 * @return bool
	 */
	public function hasChangesProperty($field = null){
		if($field === null){
			foreach($this->_property_names as $name){
				$processed = $this->_getProcessed($name);
				if($processed !== $this->getProperty($name)){
					return true;
				}
			}
		}
		if($this->_getProcessed($field) !== $this->getProperty($field)){
			return true;
		}
		return false;
	}

	/**
	 * @return string[]
	 */
	public function getChangedProperties(){
		$changed = [];
		foreach($this->_property_names as $name){
			$processed = $this->_getProcessed($name);
			if($processed !== $this->getProperty($name)){
				$changed[] = $name;
			}
		}
		return $changed;
	}


	/**
	 * @return bool
	 */
	protected function _doCreate(){
		if(parent::_doCreate()){
			foreach($this->_property_names as $name){
				if(array_key_exists($name, $this->_properties)){
					$this->_initial = $this->_schema->valueAccessSet($this->_initial, $name, $this->_properties[$name]);
				}
			}
			$this->_processed = $this->_properties;
			return true;
		}
		return false;
	}

	/**
	 * @param $changed
	 * @return bool
	 */
	protected function _doUpdate($changed){
		if(parent::_doUpdate($changed)){
			foreach($this->_property_names as $name){
				if(array_key_exists($name, $this->_properties)){
					$this->_initial = $this->_schema->valueAccessSet($this->_initial, $name, $this->_properties[$name]);
				}
			}
			$this->_processed = $this->_properties;
			return true;
		}
		return false;
	}


}



/** --------------------------------------------------------
 * ------------   @Models(InnerInteraction)   -------------------
 * ------------------------------------------------------------------ */


/**
 *
 * Метаданные это другая сторона схемы, записи связаные с источником, в Базе данных метаданные предствляют собой
 * данные по исходной таблице и её полей
 *
 * Class ModelMetadata
 * @package modelX
 * Source definition of model fields and relations
 */
class ModelMetadata{


	public function loadMetadata($modelName){

	}

}


interface ModelFieldInterface extends FieldInterface{
	
}

interface ModelRelationFieldInterface extends ModelFieldInterface{

	public function isLinked();
	
	public function getLinkedFrom();
	
	public function getRelationSchemaName();

}

/**
 * Class ModelManager
 * @package modelX
 */
abstract class ModelManager{

	/** @var  ModelCollection[] */
	protected $collections = [];

	/** @var  ModelSchema[] */
	protected $schemas = [];

	/**
	 * @param $schemaName
	 * @return bool
	 */
	public function isRecognized($schemaName){
		return isset($this->schemas[$schemaName]);
	}


	public function getCollection($modelName){

	}


	/**
	 * @param $schemaName
	 * @return SchemaOuterInteraction
	 */
	public function getSchema($schemaName){
		if(!isset($this->schemas[$schemaName])){
			$this->schemas[$schemaName] = $this->loadSchema($schemaName);
		}
		return $this->schemas[$schemaName];
	}

	/**
	 * @param $schemaName
	 * @return SchemaOuterInteraction
	 *
	 * Вот именно здесь мы и ищем и создаем схему по её установленному и полученому определению
	 *
	 */
	protected function loadSchema($schemaName){
		if(!class_exists($schemaName)){
			throw new \LogicException('Schema class "'.$schemaName.'" not found!');
		}
		$definition = $schemaName::getSchemaDefinition();
		return buildDefinition($definition);
	}

	/**
	 * @param SchemaOuterInteraction $schema
	 *
	 * По загруженой схеме инициализируется коллекция для будущих загруженных объектов
	 *
	 *
	 */
	protected function initCollection(SchemaOuterInteraction $schema){

	}

}

/**
 * Class ModelSchema
 * @package modelX
 */
abstract class ModelSchema extends RecordSchema{

	/**
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		if(!class_exists($name)){
			throw new \LogicException('Model class "' . $name . '" is not found!');
		}
		if(!is_a($name, Model::class, true)){
			throw new \LogicException('Model class name "' . $name . '" is not subclass of "' . Model::class . '"!');
		}
		return parent::setName($name);
	}

}


/**
 * Class ModelField
 * @package modelX
 */
class ModelField extends FieldOuterInteraction{

	/** @var  string */
	protected $modelFaceSetterName;

	/** @var  bool */
	protected $modelFaceSetterSoft = false;

	/** @var  string */
	protected $modelFaceGetterName;

	/** @var  bool */
	protected $modelFaceGetterSoft = false;

	/**
	 * @param string $modelFaceSetterName
	 * @param null $soft
	 * @return $this
	 */
	public function setModelFaceSetterName($modelFaceSetterName, $soft = null){
		$this->modelFaceSetterName = $modelFaceSetterName;
		if(is_bool($soft)){
			$this->modelFaceSetterSoft = $soft;
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getModelFaceSetterName(){
		return $this->modelFaceSetterName;
	}


	/**
	 * @param string $modelFaceGetterName
	 * @param null $soft
	 * @return $this
	 */
	public function setModelFaceGetterName($modelFaceGetterName, $soft = null){
		$this->modelFaceGetterName = $modelFaceGetterName;
		if(is_bool($soft)){
			$this->modelFaceGetterSoft = $soft;
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getModelFaceGetterName(){
		return $this->modelFaceGetterName;
	}

}

/**
 * Interface ModelStaticManagement
 * @package modelX
 */
interface ModelStaticManagementInterface{

	/**
	 * @param null $options
	 * @return mixed
	 */
	public static function find($options = null);

	/**
	 * @param null $options
	 * @return mixed
	 */
	public static function findFirst($options = null);

	/**
	 * @param null $options
	 * @return mixed
	 */
	public static function average($options = null);

	/**
	 * @param null $options
	 * @return mixed
	 */
	public static function sum($options = null);

	/**
	 * @param null $options
	 * @return mixed
	 */
	public static function maximum($options = null);

	/**
	 * @param null $options
	 * @return mixed
	 */
	public static function minimum($options = null);

	/**
	 * @param null $options
	 * @return mixed
	 */
	public static function count($options = null);

}

/**
 * Class Model
 * @package modelX
 *
 * Internal Special Types serialize\unserialize:
 *
 * array, objects
 * @property User $user             [1-1]
 * @property mixed[] $messages    [1-n]
 *
 *
 * Вариант преобразований.
 * 1 общий преобразователь туда, необходим в случае если мы хотим удобно для внутренней декларации Model
 * использовать свойства объекта как представления значений в БД
 * Ленивая инициализация полей - доступна при доступе через методы и\или магию
 *
 */
abstract class Model extends Record{

	/** @var  array */
	protected $_original_data = [ ];

	/**
	 * Model constructor.
	 */
	public function __construct(){}


	public function onConstruct(){ }


	/**
	 * @Starting
	 * @param array $data
	 * @return $this
	 */
	public function setOriginalData($data){
		$initial = [];
		$schema = $this->_schema;
		$this->_operation_made = self::OP_UPDATE;
		foreach($this->_property_names as $name){
			$value = $schema->valueAccessGet($data, $name);
			$this->{$name} = $initial[$name] = $value;
		}
		$this->_original_data = $initial;
		return $this;
	}

	/**
	 *
	 */
	public function getModelManager(){

	}

	public function getWriteService(){

	}

	public function getReadService(){

	}

	public function getStoredSource(){

	}


	/**
	 * @Complex-Triggered
	 * @param array $data
	 * @param null|string[]|string|int[]|int $whiteList
	 * @param null|string[]|string|int[]|int $blackList
	 * @return $this
	 */
	public function assign(array $data, $whiteList = null, $blackList = null){
		$attributes = $this->getPropertyNames();
		if($whiteList !== null){
			if(!is_array($whiteList)){
				if(!is_numeric($whiteList) || !is_string($whiteList)){
					throw new \InvalidArgumentException('White list allow value types: array or string or numeric');
				}
				$whiteList = [ $whiteList ];
			}
			$attributes = array_intersect($attributes, $whiteList);
		}
		if($blackList !== null){
			if(!is_array($blackList)){
				if(!is_numeric($blackList) || !is_string($blackList)){
					throw new \InvalidArgumentException('White list allow value types: array or string or numeric');
				}
				$blackList = [ $blackList ];
			}
			$attributes = array_diff($attributes, $blackList);
		}
		if(($readOnly = $this->getReadOnlyPropertyNames())){
			$attributes = array_diff($attributes, $readOnly);
		}
		if(($private = $this->getPrivatePropertyNames())){
			$attributes = array_diff($attributes, $private);
		}

		$setters = $this->getFacePropertySetterNames();
		foreach($attributes as $key){
			if(array_key_exists($key, $data)){

				if(isset($setters[$key])){
					$this->_setPropertyBySetter($setters[$key], $data[$key]);
					continue;
				}

				$this->{$key} = $data[$key];

			}
		}
		return $this;
	}


	/**
	 * @Complex-Triggered
	 * @param null $whiteList
	 * @param null $blackList
	 * @return array
	 */
	public function export($whiteList = null, $blackList = null){
		$attributes = $this->getPropertyNames();
		if($whiteList !== null){
			if(!is_array($whiteList)){
				if(!is_numeric($whiteList) || !is_string($whiteList)){
					throw new \InvalidArgumentException('White list allow value types: array or string or numeric');
				}
				$whiteList = [ $whiteList ];
			}
			$attributes = array_intersect($attributes, $whiteList);
		}
		if($blackList !== null){
			if(!is_array($blackList)){
				if(!is_numeric($blackList) || !is_string($blackList)){
					throw new \InvalidArgumentException('Black list allow value types: array or string or numeric');
				}
				$blackList = [ $blackList ];
			}
			$attributes = array_diff($attributes, $blackList);
		}
		$array = [ ];
		$getters = $this->getFacePropertyGetterNames();
		foreach($attributes as $key){
			if(isset($getters[$key])){
				$array[$key] = $this->_getPropertyByGetter($getters[$key]);
				continue;
			}
			if(!property_exists($this, $key)){
				$array[$key] = null;
				continue;
			}
			$array[$key] = $this->{$key};
		}
		return $array;
	}


	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function setProperty($key, $value){
		if(in_array($key, $this->getSettablePropertyNames(), true)){

			$setters = $this->getFacePropertySetterNames();
			if(isset($setters[$key])){
				$this->_setPropertyBySetter($setters[$key], $value);
				return $this;
			}

			$this->{$key} = $value;
		}
		return $this;
	}

	/**
	 * @param $key
	 * @return $this
	 */
	public function getProperty($key){
		$attributes = $this->getPropertyNames();
		if(in_array($key, $attributes, true)){

			$getters = $this->getFacePropertyGetterNames();
			if(isset($getters[$key])){
				return $this->_getPropertyByGetter($getters[$key]);
			}

			if(!property_exists($this, $key)){
				return null;
			}

			return $this->{$key};
		}
		throw new \LogicException('Property "' . $key . '" not exists in schema!');
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasProperty($key){
		$attributes = $this->getPropertyNames();
		return in_array($key, $attributes, true);
	}


	/**
	 * @Transient-state
	 * @param null|string|int|float $key
	 * @return bool
	 */
	public function hasChangesProperty($key = null){
		if($key === null){
			foreach($this->_original_data as $key => $value){
				if($value !== $this->{$key}) return true;
			}
		}elseif(isset($this->_original_data[$key])){
			return $this->_original_data[$key] !== $this->{$key};
		}
		return false;
	}


	/**
	 * @Transient-state
	 * @return array
	 */
	public function getChangedProperties(){
		$changed = [ ];
		foreach($this->_original_data as $key => $value){
			if($value !== $this->{$key}){
				$changed[] = $key;
			}
		}
		return $changed;
	}


	/**
	 * @Functionally-Access-Foundation
	 * @param $setterMethodName
	 * @param $value
	 */
	protected function _setPropertyBySetter($setterMethodName, $value){
		if(method_exists($this, $setterMethodName)){
			$this->{$setterMethodName}($value);
		}else{
			throw new \LogicException(
				'defined Setter method name "' . $setterMethodName . '" not exists in model class'
			);
		}
	}

	/**
	 * @Functionally-Access-Foundation
	 * @param $getterMethodName
	 * @return mixed
	 */
	protected function _getPropertyByGetter($getterMethodName){
		if(method_exists($this, $getterMethodName)){
			return $this->{$getterMethodName}();
		}else{
			throw new \LogicException(
				'defined Getter method name "' . $getterMethodName . '" not exists in model class'
			);
		}
	}


	/**
	 * @param null $whiteList
	 * @param null $blackList
	 * @return array
	 */
	public function getSettablePropertyNames($whiteList = null, $blackList = null){
		$attributes = $this->getPropertyNames();
		if($whiteList !== null){
			if(!is_array($whiteList)){
				if(!is_numeric($whiteList) || !is_string($whiteList)){
					throw new \InvalidArgumentException('White list allow value types: array or string or numeric');
				}
				$whiteList = [ $whiteList ];
			}
			$attributes = array_intersect($attributes, $whiteList);
		}
		if($blackList !== null){
			if(!is_array($blackList)){
				if(!is_numeric($blackList) || !is_string($blackList)){
					throw new \InvalidArgumentException('White list allow value types: array or string or numeric');
				}
				$blackList = [ $blackList ];
			}
			$attributes = array_diff($attributes, $blackList);
		}
		if(($readOnly = $this->getReadOnlyPropertyNames())){
			$attributes = array_diff($attributes, $readOnly);
		}
		if(($private = $this->getPrivatePropertyNames())){
			$attributes = array_diff($attributes, $private);
		}
		return $attributes;
	}


	/**
	 * @Meta-Information
	 * @return array
	 */
	public function getPrivatePropertyNames(){ return [ ]; }

	/**
	 * @Meta-Information
	 * @return array
	 */
	public function getReadonlyPropertyNames(){ return [ ]; }

	/**
	 * @Meta-Information
	 * @return array
	 */
	public function getPropertyNames(){ return [ ]; }

	/**
	 * @Meta-Information
	 * @return array
	 */
	public function getPropertyMap(){ return [ ]; }


	/**
	 * @Functionally-Access-Foundation
	 * @return array
	 */
	public function getFacePropertySetterNames(){ return [ ]; }

	/**
	 * @Functionally-Access-Foundation
	 * @return array
	 */
	public function getFacePropertyGetterNames(){ return [ ]; }


	public function afterFetch(){ }

	public function beforeSave(){ }


	/**
	 * @Create
	 * @Update
	 */
	public function save(){

		/**
		 *
		 * Check save type: CREATE | UPDATE
		 */

		/**
		 * Save relations
		 */

		/**
		 * later save current object
		 */

	}

	protected function _create(){

	}

	protected function _update(){

	}

	public function delete(){

		/**
		 * Dependency relations
		 * relation objects delete
		 * Foreign keys CASCADE
		 */


		/**
		 * Prepare Source Query
		 */


	}

	/**
	 *
	 */
	public function getRelation(){

	}


}

class ModelInitialize{


	public function __construct(){

		$model = new User();

		/**
		 * Присвоение модели определенных ей свойственных данных
		 */
		$model->initialFill(
			[
				//'id' => 5, // Айди чаще всего может быть read-only параметром
				'name' => 'Alexey',
				'city' => 'Khabarovsk'

			],
			$model::OP_MADE_READY
		);


		/**
		 * Вызывать для того чтобы разработчик смог выставить нужные типы данных для полей которые мы выставили.
		 * Так реализовано в Phalcon
		 *
		 *
		 *
		 */
		$model->afterFetch();

		/**
		 * То-же самое, но в обратном направлении
		 */
		$model->beforeSave();

	}

}


/**
 * Class User
 * @package modelX
 */
class User extends Model{

	/** @var int */
	protected $id; //read-only

	/** @var string */
	protected $name;

	/** @var string */
	protected $city;

	/** @var array */
	protected $data = [ ]; // работа с массивом для базы данных:  обычно сериализуется и десериализуется


	/**
	 * @param $name
	 */
	public function setName($name){
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @Meta-Information
	 * @return array
	 */
	public function getReadonlyPropertyNames(){
		return [ 'id' ];
	}

	/**
	 * @Meta-Information
	 * @return array
	 */
	public function getPropertyNames(){
		return [ 'id', 'name', 'city' ];
	}

	/**
	 * @Meta-Information
	 * @return array
	 */
	public function getPropertyMap(){
		return [
			'id'   => 0,
			'name' => 1,
			'city' => 2
		];
	}

	/**
	 * @Functionally-Access-Foundation
	 * @return array
	 */
	public function getFacePropertySetterNames(){
		return [
			'name' => 'setName'
		];
	}

	/**
	 * @Functionally-Access-Foundation
	 * @return array
	 */
	public function getFacePropertyGetterNames(){
		return [
			'name' => 'getName'
		];
	}


	public function afterFetch(){
		/***
		 * В этом методе в варианте с Phalcon мы делаем работу за типизатора который бы должен это делать самостоятельно
		 */
		if(is_string($this->data)){
			$this->data = unserialize($this->data);
		}
	}


	/**
	 *
	 */
	public function beforeSave(){
		if(is_array($this->data)){
			$this->data = serialize($this->data);
		}
	}


}


/** -----------------------------------------------------
 * -----------   @Record-Storing   ---------------------
 * --------------------------   @Storage   ------------
 * ---------------   @Source   -----------------------
 * -------------------------------------------------- */

/**
 * Interface DataBaseAccessInterface
 * @package modelX
 */
interface StorageInterface{

	/**
	 * @param $data
	 * @param $source
	 * @return bool
	 */
	public function create($data,$source);

	/**
	 * @param $condition
	 * @param $data
	 * @param $source
	 * @return bool
	 */
	public function update($data,$condition, $source);

	/**
	 * @param $condition
	 * @param $source
	 * @return bool
	 */
	public function delete($condition, $source);

	/**
	 * @param $columns
	 * @param $source
	 * @param $condition
	 * @param null $limit
	 * @param null $offset
	 * @param null $orderBy
	 * @param array $options
	 * @return array
	 */
	public function select($columns, $source, $condition, $limit = null, $offset = null, $orderBy = null,array $options = null);

}

/**
 * Interface ModelSchemaInterface
 * @package modelX
 */
interface ModelSchemaInterface{

	public function getReadConnectionService($model);

	public function getWriteConnectionService($model);

	public function getSource($model);

}

/**
 * Class Storage
 * @package modelX
 */
abstract class Storage implements StorageInterface{

}

/**
 * Interface SourceMetaInterface
 * @package modelX
 */
interface SourceMetaInterface{

	public function setField($fieldName, $vartype, $default = null);

	public function getFieldVartype($fieldName);

	public function getFieldDefault($fieldName);

	public function getFieldNames();

}

interface SourceDataContainerTypeAwareInterface{

	public function setContainerType($containerTypeName);

	public function getContainerType();

}


/**
 * Interface SchemaWorkspaceInterface
 * @package modelX
 */
interface SchemaWorkspaceInterface{

	/**
	 * @param $schema_alias
	 * @return Schema|null
	 */
	public function getSchema($schema_alias);

}


/**-------------------------------------------------------
 * ----------------    @Conditional   -----------------------
 * ------------------------------------------  Where  -------*/


/**
 * Interface ConditionInterface
 * @package modelX
 */
interface ConditionInterface{

	/**
	 * @param PropertyRegistryInterface|mixed $data
	 * @param null|OuterValueAccessAwareInterface|callable $access - if data is outer original data
	 * @return mixed
	 */
	public function __invoke($data, $access = null);
	
}

/**
 * Interface ConditionTargetInterface
 * @package modelX
 */
interface ConditionTargetInterface extends ConditionInterface{

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setField($name);

	/**
	 * @param string $operator_definition
	 * @return $this
	 */
	public function setOperator($operator_definition);

	/**
	 * @param $wanted
	 * @return $this
	 */
	public function setWanted($wanted);
	
}

/**
 * Interface ConditionBlockInterface
 * @package modelX
 */
interface ConditionBlockInterface extends ConditionInterface{

	const OP_AND = 'AND';
	const OP_OR  = 'OR';

	/**
	 * @param ConditionInterface $condition
	 * @return $this
	 */
	public function addCondition(ConditionInterface $condition);

	/**
	 * @param string $delimiter
	 * @return $this
	 */
	public function addDelimiter($delimiter = 'and');
	
}

/**
 * Class ConditionBlock
 * @package modelX
 */
class ConditionBlock implements ConditionBlockInterface{

	/** @var  array */
	protected $conditions = [ ];

	/** @var  array */
	protected $operators = [ ];

	/**
	 * @param PropertyRegistryInterface|mixed $data
	 * @param null|OuterValueAccessAwareInterface|callable $access - if data map is outer original data
	 * @return mixed
	 */
	public function __invoke($data, $access = null){
		$operator = null;
		foreach($this->conditions as $i => $condition){
			if($operator === 'and' && isset($value)){
				$value = $value && call_user_func($condition, $data, $access);
			}elseif($operator === 'or' && isset($value)){
				$value = $value || call_user_func($condition, $data, $access);
			}else{
				$value = call_user_func($condition, $data, $access);
			}
			$operator = $this->operators[$i];
		}
		if(isset($value)){
			return $value;
		}else{
			return true;
		}
	}

	/**
	 * @param ConditionInterface $condition
	 * @param null $nextOperator
	 * @return $this
	 */
	public function addCondition(ConditionInterface $condition, $nextOperator = null){
		$this->conditions[] = $condition;
		if($nextOperator !== null){
			$this->operators[] = strtolower($nextOperator);
		}
		return $this;
	}

	/**
	 * @param string $delimiter
	 * @return $this
	 */
	public function addDelimiter($delimiter = 'and'){
		$this->operators[] = strtolower($delimiter);
		return $this;
	}
}

/**
 * Class Condition
 * @package modelX
 */
class Condition implements ConditionTargetInterface{

	/** @var  string */
	protected $field;

	/** @var  string */
	protected $operator;

	/** @var  mixed */
	protected $wanted;

	/**
	 * @param PropertyRegistryInterface|mixed $data
	 * @param null|OuterValueAccessAwareInterface|callable $access - if data map is outer original data
	 * @return mixed
	 */
	public function __invoke($data, $access = null){
		return \Jungle\CodeForm\LogicConstruction\Condition::collateRaw(
			OuterValueAccess::handleAccessGet($access, $data, $this->field),
			$this->operator,
			$this->wanted
		);
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setField($name){
		$this->field = $name;
		return $this;
	}

	/**
	 * @param string $operator_definition
	 * @return $this
	 */
	public function setOperator($operator_definition){
		$this->operator = $operator_definition;
		return $this;
	}

	/**
	 * @param mixed $wanted
	 * @return $this
	 */
	public function setWanted($wanted){
		$this->wanted = $wanted;
		return $this;
	}
}

/**
 * Class ConditionComplex
 * @package modelX
 */
class ConditionComplex extends ConditionBlock implements PredicatedConditionInterface{

	/** @var array */
	protected $predicates = [ ];

	/** @var */
	protected static $over_getter;

	/**
	 * @param array $collated_data
	 * @return array
	 * @throws \Exception
	 */
	public function setPredicatedData(array $collated_data){
		if(!$this->checkPredicates($collated_data)){
			throw new \LogicException('Predicates is not collated with current condition');
		}
		$this->predicates = $collated_data;
		return $this;
	}

	/**
	 * @param array $predicates
	 * @return mixed
	 */
	public function checkPredicates(array $predicates){
		if(!self::$over_getter){
			self::$over_getter = function ($data, $key){
				return $data[$key];
			};
		}
		return $this->__invoke($predicates, self::$over_getter);
	}

	/**
	 * @return array
	 */
	public function getPredicatedData(){
		return $this->predicates;
	}
}

/**
 * Interface PredicatedConditionInterface
 * @package modelX
 */
interface PredicatedConditionInterface{

	/**
	 * @param array $collated_data
	 * @return array
	 */
	public function setPredicatedData(array $collated_data);

	/**
	 * @return array
	 */
	public function getPredicatedData();

}





/** ----------------------------------------------------------------------
 * --------------------- @Example -----------------------------
 * ------------------------------------------------------------------------- */


/**
 * Class ExampleDataMap
 * @package modelX
 */
class ExampleStage1DataMap extends DataMap{

	/**
	 * SimpleDataMap constructor.
	 * @param SchemaOuterInteraction $schema
	 * @param $originalData
	 */
	public function __construct(SchemaOuterInteraction $schema, $originalData){
		$this->setSchema($schema);
		$this->setOriginalData($originalData);
	}


}

/**
 * Class ExampleStage1SchemaOuterInteraction
 * @package modelX
 */
class ExampleStage1SchemaOuterInteraction extends SchemaOuterInteraction{

	/**
	 * ExampleStage1SchemaOuterInteraction constructor.
	 * @param FieldOuterInteraction[] $fields
	 * @param array $indexes
	 */
	public function __construct(array $fields, array $indexes = [ ]){
		$this->fields = $fields;
		$this->indexes = $indexes;
	}

}

/**
 * Class ExampleStage1FieldOuterInteraction
 * @package modelX
 */
class ExampleStage1FieldOuterInteraction extends FieldOuterInteraction{

}

/**
 * Class ExampleStage1Index
 * @package modelX
 */
class ExampleStage1Index extends Index{
	/**
	 * ExampleStage1Index constructor.
	 * @param $name
	 * @param $type
	 */
	public function __construct($name, $type){
		$this->setName($name);
		$this->setType($type);
	}
}
