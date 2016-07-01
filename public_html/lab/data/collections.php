<?php
/**
 * Файл был создан при первом проектировании DataMapCollection
 *
 *
 *
 *
 */


$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
	'Jungle' => dirname(dirname(__DIR__)) .  '/core/Jungle/'
]);
$loader->register();
echo '<pre>';


/**
 * Class Cmp
 */
abstract class Sorter implements \Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools\ISorter{

	/** @var */
	protected $cmp;

	/** @var */
	protected $sort_direction = self::SORT_TYPE_ASC;

	/** @var callable[] */
	protected static $cmp_collection = [];

	/**
	 * @return callable|Closure|null
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
		}elseif($alias==='default'){
			self::$cmp_collection[$alias] = function($a,$b){
				if($a==$b){
					return 0;
				}
				return $a<$b?-1:1;
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
		self::$cmp_collection[$alias] = $cmp;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	function setDirection($type = self::SORT_TYPE_ASC){
		if(!in_array($type,[self::SORT_TYPE_ASC,self::SORT_TYPE_DESC],true)){
			throw new \LogicException('Sort direction invalid "'.$type.'" ');
		}
		$this->sort_direction = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDirection(){
		return $this->sort_direction;
	}

	/**
	 * @param callable $sorter
	 * @return mixed
	 */
	function setCmp(callable $sorter = null){
		$this->cmp = $sorter;
	}

	/**
	 * @return callable|Closure|null
	 */
	public function getCmp(){
		if(!$this->cmp){
			$this->cmp = self::getDefaultCmp();
		}
		return $this->cmp;
	}

}

class DataMapCollectionType{

	protected $name;

	protected $value_accessor;

}

/**
 * Class DataMapConverter
 */
class DataMapConverter{

	/** @var  null|array */
	protected $field_map;

	/**
	 * @param $data
	 * @param DataMapCollection $collection
	 * @return mixed
	 */
	public function __invoke($data, DataMapCollection $collection){
		return $data;
	}

	/**
	 * @param null|array $fields
	 * @return $this
	 */
	public function setFieldMap($fields = null){
		$this->field_map = is_array($fields)?array_flip($fields):null;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getFieldMap(){
		return $this->field_map;
	}

}



/**
 * Class DataMapCollectionReader
 */
class DataMapCollectionReader implements \SeekableIterator, \Countable, \ArrayAccess {

	/** @var  null|int */
	protected $limit;

	/** @var int  */
	protected $offset = 0;

	/** @var DataMapCollection */
	protected $collection;

	/** @var  int */
	protected $i;


	/**
	 * @param DataMapCollection|array $collection
	 * @return $this
	 */
	public function setCollection($collection){
		$this->collection = $collection;
		return $this;
	}

	/**
	 * @return DataMapCollection
	 */
	public function getCollection(){
		return $this->collection;
	}

	/**
	 * @param null $limit
	 * @return $this
	 */
	public function setLimit($limit = null){
		$this->limit = $limit;
		return $this;
	}

	/**
	 * @param $offset
	 * @return $this
	 */
	public function setOffset($offset){
		$this->offset = $offset;
		return $this;
	}

	/**
	 * @return mixed|null
	 */
	public function current(){
		$collectionIndex = $this->i + $this->offset;
		return $this->collection[$collectionIndex];
	}

	/**
	 *
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
		if(!is_null($this->limit) && $this->i >= $this->limit){
			return false;
		}
		$collectionIndex = $this->i + $this->offset;
		return isset($this->collection[$collectionIndex]);
	}

	/**
	 * Rewind
	 */
	public function rewind(){
		$this->i = 0;
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset){
		$collectionIndex = $this->i + $this->offset;
		return isset($this->collection[$collectionIndex]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed|null
	 */
	public function offsetGet($offset){
		$collectionIndex = $this->i + $this->offset;
		return $this->collection[$collectionIndex];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value){}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset){}

	/**
	 * @return int|null
	 */
	public function count(){
		$seek_count = count($this->collection) - $this->offset;
		if($this->limit!==null){
			if($this->limit > $seek_count){
				return $seek_count;
			}else{
				return $this->limit;
			}
		}else{
			return $seek_count;
		}
	}

	/**
	 * @param int $position
	 */
	public function seek($position){
		$this->i = $position;
		if($this->limit!==null && $this->i > $this->limit){
			$this->i = $this->limit - 1;
		}
	}
}

/**
 * Interface DataMapValidatorInterface
 */
interface DataMapValidatorInterface{

	/**
	 * @param $value
	 * @param null $property
	 * @return bool|array
	 */
	public function check($value, $property = null);

	/**
	 * @param $value
	 * @param $property
	 * @return bool|array
	 */
	public function __invoke($value,$property = null);

}

/**
 * Class DataMapValidator
 */
class DataMapValidator implements DataMapValidatorInterface, \Jungle\Util\INamed{

	protected $error_message;

	protected $name;

	/**
	 * @param DataMapValidatorInterface|callable|null $validator
	 * @return callable|DataMapValidatorInterface|null
	 */
	public static function checkoutValidator($validator = null){
		if(is_callable($validator) || is_null($validator)){
			if(is_object($validator) && !$validator instanceof \Closure && !$validator instanceof DataMapValidatorInterface){
				throw new \LogicException('VALIDATOR is not valid validator object (not instanceof \DataMapValidatorInterface)');
			}
			return $validator;
		}else{
			throw new \LogicException('Validator invalid');
		}
	}

	/**
	 * @param $property
	 * @return string
	 */
	public function getMessage($property = null){
		if($property==null){
			$property = '{anonymous_property}';
		}
		return str_replace(['[[property]]','[[validator_type]]'],[$property,$this->getName()],$this->error_message);
	}

	/**
	 * @param array $options
	 */
	public function __construct(array $options = []){

		if(isset($options['message'])){
			$this->setErrorMessage($options['message']);
		}

	}

	public function setErrorMessage($error_message){
		$this->error_message  = $error_message;
		return $this;
	}

	/**
	 * @param $value
	 * @param $property
	 * @return bool
	 */
	public function check($value,$property = null){
		return true;
	}

	/**
	 * @param $value
	 * @param null $property
	 * @return array|bool|string
	 */
	public function __invoke($value,$property=null){
		return $this->check($value,$property);
	}

	/**
	 * Получить имя объекта
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Выставить имя объекту
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}
}

/**
 * Class DataMapValidatorAggregate
 */
class DataMapValidatorAggregate implements DataMapValidatorInterface{

	/**
	 * @var DataMapValidator[]|callable[]
	 */
	protected $validators = [];

	/**
	 * @param array $validators
	 */
	public function __construct(array $validators){
		$this->validators = $validators;
	}

	/**
	 * @param $value
	 * @param null $property
	 * @return array|bool|string
	 */
	public function __invoke($value,$property=null){
		return $this->check($value,$property);
	}

	/**
	 * @param $value
	 * @param null $property
	 * @return bool|array
	 */
	public function check($value, $property = null){
		$errors = [];
		foreach($this->validators as $validator){
			$result = call_user_func($validator,$value,$property);
			if($result!==true){
				$errors = array_merge($errors,$this->decompositeErrors($result));
			}
		}
		if($errors){
			return $errors;
		}else{
			return true;
		}
	}


	/**
	 * @param $errors
	 * @return array|bool
	 */
	public static function decompositeErrors($errors){
		if(is_array($errors) && isset($errors['message']) || is_string($errors)){
			return [$errors];
		}
		return $errors;
	}


}

/**
 * Class DataMapSchemaField
 */
class DataMapSchemaField implements \Jungle\Util\INamed{

	/** @var string|int */
	protected $original_key;

	/** @var string|int */
	protected $native_key;

	/** @var DataMapValueAccessSetter|callable|null */
	protected $setter;

	/** @var DataMapValueAccessGetter|callable|null */
	protected $getter;

	/** @var  string */
	protected $type;

	/** @var  DataMapValidatorInterface|callable|null */
	protected $validator;

	/**
	 * @param $name
	 */
	public function __construct($name){
		$this->setName($name);
	}

	/**
	 * Получить имя объекта
	 * @return string
	 */
	public function getName(){
		return $this->native_key;
	}

	/**
	 * Выставить имя объекту
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->native_key = $name;
		return $this;
	}

	/**
	 * @param string|int|callable $key
	 * @return $this
	 */
	public function setOriginalKey($key){
		$this->original_key = $key;
		return $this;
	}

	/**
	 * @return callable|int|string
	 */
	public function getOriginalKey(){
		return $this->original_key;
	}

	/**
	 * @param callable|DataMapValueAccessSetter|string|null $setter
	 * @return $this
	 */
	public function setSetter($setter = null){
		$this->setter = DataMapValueAccess::checkoutSetter($setter);
		return $this;
	}

	/**
	 * @return callable|DataMapValueAccessSetter|null
	 */
	public function getSetter(){
		return $this->setter;
	}

	/**
	 * @param callable|DataMapValueAccessGetter|string|null $getter
	 * @return $this
	 */
	public function setGetter($getter = null){
		$this->getter = DataMapValueAccess::checkoutGetter($getter);
		return $this;
	}

	/**
	 * @return callable|DataMapValueAccessGetter|null
	 */
	public function getGetter(){
		return $this->getter;
	}


	/**
	 * @param DataMapValidatorInterface|callable|null $validator
	 * @return $this
	 */
	public function setValidator($validator = null){
		$this->validator = DataMapValidator::checkoutValidator($validator);
		return $this;
	}

	/**
	 * @return DataMapValidatorInterface|callable|null
	 */
	public function getValidator(){
		return $this->validator;
	}


	/**
	 * @param $value
	 * @return bool
	 */
	public function leadType($value){
		if($this->type!==null){
			settype($value,$this->type);
		}
		return $value;
	}

	/**
	 * @return string
	 */
	public function getType(){
		return $this->type;
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
	 * @param $value
	 * @return bool|array
	 */
	public function validate($value){
		if($this->validator){
			return DataMapValidatorAggregate::decompositeErrors(
				call_user_func($this->validator,$value,$this->getName())
			);
		}
		return true;
	}

	/**
	 * @param $data
	 * @param callable $default_getter
	 * @return mixed field value
	 */
	public function callGetter($data,callable $default_getter){
		$getter = $this->getGetter();
		if(!$getter){
			$getter = $default_getter;
		}
		return call_user_func($getter,$data,$this->getOriginalKey());
	}

	/**
	 * @param $data
	 * @param $value
	 * @param callable $default_setter
	 * @return mixed data set
	 */
	public function callSetter($data,$value,callable $default_setter){
		$setter = $this->getSetter();
		if(!$setter){
			$setter = DataMapValueAccess::checkoutSetter($default_setter);
		}
		return call_user_func($setter,$data,$this->getOriginalKey(),$value);

	}

}

/**
 * Class DataMapSchema
 */
class DataMapSchema{

	/** @var  DataMapSchemaField[]|null */
	protected $fields;

	/** @var  callable|DataMapConverter|null */
	protected $data_converter;

	/** @var  DataMapValidatorInterface|callable|null */
	protected $validator;

	/** @var  callable|DataMapValueAccessGetter|null */
	protected $value_access_getter;

	/** @var callable|null */
	protected $value_access_setter;

	/**
	 * @param DataMapSchemaField[]|null $fields
	 * @param $original_order
	 * @return $this
	 */
	public function setFields(array $fields = null,$original_order = false){
		if($fields!==null && $original_order){
			foreach($fields as $i=>$field){
				$field->setOriginalKey($i);
			}
		}
		$this->fields = $fields;
		return $this;
	}

	/**
	 * @return DataMapSchemaField[]|null
	 */
	public function getFields(){
		return $this->fields;
	}

	/**
	 * @param callable|DataMapConverter|null $converter
	 * @return $this
	 */
	public function setDataConverter(callable $converter = null){
		$this->data_converter = $converter;
		return $this;
	}

	/**
	 * @return callable|DataMapConverter|null
	 */
	public function getDataConverter(){
		return $this->data_converter;
	}

	/**
	 * @param callable|DataMapValueAccessGetter|null $getter
	 * @return $this
	 */
	public function setValueAccessGetter(callable $getter = null){
		$this->value_access_getter = $getter;
		return $this;
	}

	/**
	 * @return callable|DataMapValueAccessGetter|null
	 */
	public function getValueAccessGetter(){
		return $this->value_access_getter;
	}

	/**
	 * @param callable|null $setter
	 * @return $this
	 */
	public function setValueAccessSetter(callable $setter = null){
		$this->value_access_setter = $setter;
		return $this;
	}

	/**
	 * @return callable|null
	 */
	public function getValueAccessSetter(){
		return $this->value_access_setter;
	}

	/**
	 * @return bool
	 */
	public function isOriginalActual(){
		return $this->fields === null;
	}

	/**
	 * @return callable|null
	 */
	public function isDataModified(){
		return $this->data_converter;
	}


	/**
	 * @param $name
	 * @return DataMapSchemaField|null
	 */
	public function getField($name){
		return \Jungle\Util\Value\Massive::getNamed($this->fields,$name,'strcmp');
	}

	/**
	 * @param $name
	 * @return bool|int
	 */
	public function getFieldIndex($name){
		return \Jungle\Util\Value\Massive::searchNamed($this->fields,$name,'strcmp');
	}

	/**
	 * @param $name
	 * @return \Jungle\Util\INamed|null
	 */
	public function getFieldType($name){
		if(($field = $this->getField($name))){
			return $field->getType();
		}else{
			return null;
		}
	}

	public function setValidator(callable $validator = null){

	}

	public function getValidator(){

	}

	/**
	 * @param $data
	 * @return bool|array
	 */
	public function validate($data){
		if($this->validator){
			return call_user_func($this->validator,$data,'{data_object}');
		}
		return true;
	}


	/**
	 * @param $data
	 * @return mixed
	 */
	public function represent($data){
		if($this->data_converter){
			$data = call_user_func($this->data_converter,$data, $this);
		}
		return $data;
	}

	/**
	 * @param $represented_data
	 * @param $field_name
	 * @param callable $default_getter
	 * @return mixed
	 */
	public function fieldGetter($represented_data, $field_name, callable $default_getter){
		$getter = $this->getValueAccessGetter();
		if(!$getter){
			$getter = $default_getter;
		}
		if($this->isOriginalActual()){
			return call_user_func($getter,$represented_data, $field_name);
		}else{
			if(($field = $this->getField($field_name))){
				return $field->callGetter($represented_data,$getter);
			}else{
				throw new \LogicException('Access to not declared field "'.$field_name.'"!');
			}
		}
	}

	/**
	 * @param $represented_data
	 * @param $field_name
	 * @param $value
	 * @param callable $default_setter
	 * @return mixed
	 */
	public function fieldSetter($represented_data, $field_name, $value, callable $default_setter){
		$setter = $this->getValueAccessSetter();
		if(!$setter) $setter = $default_setter;

		if($this->isOriginalActual()){
			$represented_data = call_user_func($setter,$represented_data,$field_name, $value);
		}else{
			if(($field = $this->getField($field_name))){
				$represented_data = $field->callSetter($represented_data,$value,$setter);
			}else{
				throw new \LogicException('Access to not declared field "'.$field_name.'"!');
			}
		}

		return $represented_data;

	}

}

/**
 * Class DataMapCollection
 *
 * Collection[]
 *      element{data}
 *      [data_converter] data = conversion | [skip_conversion] data = data
 *              Конвертер данных - выдает свой набор данных с кастомными полями
 *      [field_map] field_name = field_alias | [skip_field_map] field_name = field_name
 *              Набор псевдо полей - дает псевдоним для исходного поля из преобразованного или реального набора данных
 *      [value_accessor] data[field_name]
 *              Доступ к значению производится посредством псевдонима или реального названия поля,
 *              по полученому набору либо реальных или пребразованных данных
 */
class DataMapCollection implements \SeekableIterator, \Countable, \ArrayAccess{

	/**
	 * @var array
	 * Набор элементов коллекции
	 */
	protected $collection = [];

	/**
	 * @var  DataMapSchema
	 * $field_map
	 * $data_converter
	 * $value_accessor
	 */
	protected $schema;

	/**
	 * @var callable|DataMapValueAccessGetter
	 */
	protected $value_access_getter;

	/**
	 * @var callable|DataMapValueAccessSetter
	 */
	protected $value_access_setter;

	/**
	 * @var  callable|DataMapCriteria|null
	 * Критерия содержимого.
	 */
	protected $contain_criteria;

	/**
	 * @var  array|string
	 * @see sortBy
	 * Список полей с направлениями сортировки,
	 * поля берутся из элемента коллекции или из преобразованного набора данных
	 */
	protected $sort_by;

	/**
	 * @var callable
	 * Cache sorter
	 */
	protected $sort_by_callback;

	/**
	 * @var  callable
	 * @see  sortBy
	 * Функция сортировки значения из набора данных
	 * взятого посредством $value_accessor из
	 * элемента коллекции или преобразованного набора данных
	 */
	protected $sorter_cmp;

	/**
	 * @var  string[]|string
	 */
	protected $group_by;


	/**
	 * @param DataMapSchema $schema
	 * @param array $collection
	 */
	public function __construct(DataMapSchema $schema, array $collection){
		$_c = [];
		foreach($collection as $item){
			if($schema->validate($item)===true){
				$_c[] = $schema->represent($item);
			}
		}
		$this->collection = $_c;
		$this->setSchema($schema);
	}

	/**
	 * @param DataMapSchema $schema
	 * @return $this
	 */
	public function setSchema(DataMapSchema $schema){
		$this->schema = $schema;
		return $this;
	}

	/**
	 * @return DataMapSchema
	 */
	public function getSchema(){
		return $this->schema;
	}



	/**
	 * @param callable|DataMapValueAccessGetter|null|string $getter
	 * @return $this
	 */
	public function setValueAccessGetter($getter = null){
		$this->value_access_getter = DataMapValueAccess::checkoutGetter($getter);
		return $this;
	}

	/**
	 * @return callable|DataMapValueAccessGetter
	 */
	public function getValueAccessGetter(){
		if(!$this->value_access_getter){
			$this->value_access_getter = DataMapValueAccess::getDefaultGetter();
		}
		return $this->value_access_getter;
	}



	/**
	 * @param callable|DataMapValueAccessSetter|null|string $setter
	 * @return $this
	 */
	public function setValueAccessSetter($setter = null){
		$this->value_access_setter = DataMapValueAccess::checkoutSetter($setter);
		return $this;
	}

	/**
	 * @return callable|DataMapValueAccessSetter
	 */
	public function getValueAccessSetter(){
		if(!$this->value_access_setter){
			$this->value_access_setter = DataMapValueAccess::getDefaultSetter();
		}
		return $this->value_access_setter;
	}


	/**
	 * @param callable $sorter
	 * @return $this
	 */
	public function setSorterCmp(callable $sorter){
		$this->sorter_cmp = $sorter;
		return $this;
	}

	/**
	 * @return callable|Closure|null
	 */
	public function getSorterCmp(){
		if(!$this->sorter_cmp){
			$this->sorter_cmp = Sorter::getDefaultCmp();
		}
		return $this->sorter_cmp;
	}

	/**
	 * @return $this
	 */
	public function sort(){
		if(!$this->sort_by_callback){
			$this->sort_by_callback = function($dataA,$dataB){
				$fields = $this->getSortByFields();
				if($fields!==null){
					$results = [];
					foreach($fields as $field_name => $direction){
						$returned = call_user_func(
							$this->getSorterCmp(),
							$this->valueAccessGet($dataA,$field_name),
							$this->valueAccessGet($dataB,$field_name)
						);
						if($returned !== 0 && $direction === 'DESC'){
							$returned = $returned < 0?1:-1;
						}
						$results[] = $returned;
					}
					foreach($results as $result){
						if($result!==0){
							return $result;
						}
					}
				}
				return 0;
			};
		}
		usort($this->collection,$this->sort_by_callback);
		return $this;
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
		}else{
			$_fields = $fields;
		}

		if($this->sort_by!==$_fields){
			$this->sort_by = $_fields;
			$this->sort();
		}

		return $this;
	}

	/**
	 * TODO
	 * @param $fields
	 * @return $this
	 */
	public function groupBy($fields){
		$this->group_by = $fields;
		return $this;
	}

	/**
	 * @return array|string[]
	 */
	public function getSortByFields(){
		return $this->sort_by;
	}

	/**
	 * @return string|string[]
	 */
	public function getGroupByFields(){
		return $this->group_by;
	}

	/**
	 * @param $data
	 * @param $required_field_name
	 * @return mixed
	 */
	public function valueAccessGet($data, $required_field_name){
		return $this->getSchema()->fieldGetter($data,$required_field_name,$this->getValueAccessGetter());
	}

	/**
	 * @param $data
	 * @param $required_field_name
	 * @param $value
	 * @return mixed
	 */
	public function valueAccessSet($data, $required_field_name, $value){
		return $this->getSchema()->fieldSetter($data,$required_field_name,$value,$this->getValueAccessSetter());
	}

	/**
	 * @param $item
	 * @return array
	 */
	public function getRow($item){
		$row = [];
		foreach($this->getFieldNames() as $name){
			$row[] = $this->valueAccessGet($item,$name);
		}
		return $row;
	}

	/**
	 * @return array
	 */
	public function getFieldNames(){
		return \Jungle\Util\Value\Massive::getNames($this->getSchema()->getFields());
	}



	/**
	 * @param $criteria
	 * @return array
	 */
	public function collect(callable $criteria){
		$collected = [];
		foreach($this->collection as $item){
			$returned = call_user_func($criteria,$item,$this);
			if($returned){
				$collected[] = $item;
			}
		}
		return $collected;
	}



	/** @var  DataMapCollection */
	protected $ancestor;

	/** @var DataMapCollection[]  */
	protected $descendants = [];

	/**
	 * @param callable|null $criteria
	 * @return DataMapCollection
	 */
	public function extend(callable $criteria = null){

	}

	/**
	 * @param $item
	 */
	public function push($item){

	}

	protected function _push($item, $propagateTop = true, $propagateDescendants = true){

	}

	/** @var  int */
	protected $i;

	/**
	 * @return mixed
	 */
	public function current(){
		return $this->collection[$this->i];
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
		return isset($this->collection[$this->i]);
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
		return isset($this->collection[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return null
	 */
	public function offsetGet($offset){
		return isset($this->collection[$offset])?$this->collection[$offset]:null;
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
		return count($this->collection);
	}

	/**
	 * @param int $position
	 */
	public function seek($position){
		$this->i = $position;
	}

}
/**
 * Class DataMapCollectionSorter
 */
class DataMapCollectionSorter extends Sorter{

	/** @var  callable|null */
	protected $column_accessor;

	/** @var string|int */
	protected $sort_column;


	/**
	 * @param callable $callable - args($data, $orderColumn, Cmp $sorter)
	 * @return $this
	 */
	function setColumnAccess(callable $callable = null){
		$this->column_accessor = $callable;
		return $this;
	}

	/**
	 * @param array $array
	 * @return mixed
	 */
	function sort(array & $array){
		$result = usort($array,function($a,$b){
			if($this->column_accessor){
				$a = call_user_func($this->column_accessor,$a,$this->sort_column,$this);
				$b = call_user_func($this->column_accessor,$b,$this->sort_column,$this);
			}
			return call_user_func($this->getCmp(),$a,$b);
		});
		if($result!==false && $this->sort_direction === self::SORT_TYPE_DESC){
			$array = array_reverse($array);
		}
		return $result;
	}


	/**
	 * @param array|string $column
	 * @param null $sortType
	 * @return $this
	 */
	function setOrderBy($column, $sortType = null){
		if($sortType!==null){
			$this->setDirection($sortType);
		}
		$this->sort_column = $column;
	}


	/**
	 * @return string|null
	 */
	public function getOrderBy(){
		return $this->sort_column;
	}



}

/**
 * Interface DataMapCriteriaInterface
 */
interface DataMapCriteriaInterface{

	/**
	 * @param $item
	 * @param DataMapCollection $collection
	 */
	public function __invoke($item, DataMapCollection $collection);

}

/**
 * Class DataMapCriteria
 */
class DataMapCriteria implements DataMapCriteriaInterface{


	const OP_OR = 'OR';

	const OP_AND = 'AND';


	/** @var   */
	protected $field;

	/** @var   */
	protected $operator;

	/** @var   */
	protected $wanted_value;

	/**
	 * @param $item
	 * @param DataMapCollection $collection
	 * @return mixed
	 */
	public function __invoke($item, DataMapCollection $collection){
		$value = $collection->valueAccessGet($item,$this->field);
		$condition = \Jungle\CodeForm\LogicConstruction\Condition::getCondition($value,$this->operator,$this->wanted_value);
		$execute =  $condition->execute();
		return $execute;
	}

	public function __construct($field,$operator,$wanted){
		$this->field        = $field;
		$this->operator     = $operator;
		$this->wanted_value = is_numeric($wanted)?floatval($wanted):$wanted;
	}

	/**
	 * @param $definition
	 * @return DataMapCriteriaBlock
	 */
	public static function build($definition){

		if(is_array($definition)){
			$criteria = new DataMapCriteriaBlock();
			foreach($definition as $key => $value){

				if(is_string($key) && is_string($value)){
					$key = trim($key,"\r\n\t\0\x0B:");

					list($key,$operator) = explode(':',$key);
					if(!$operator)$operator = '=';

					$wanted = trim($value,"\r\n\t\0\x0B:");

					list($wanted, $conditionDelimiter) = explode(':',$wanted);

					if(!$conditionDelimiter){
						$conditionDelimiter = 'AND';
					}

					$criteria->addCondition(new DataMapCriteria($key,$operator,$wanted));
					$criteria->addOperator($conditionDelimiter);
				}elseif(is_string($key) && is_array($value)){
					$key = trim($key,"\r\n\t\0\x0B:");
					list($key,$operator,$wanted,$conditionDelimiter) = explode(':',$key);
					if(!$operator)$operator = '=';
					if(!$conditionDelimiter){
						$conditionDelimiter = 'AND';
					}
					$criteria->addCondition(new DataMapCriteria($key,$operator,$wanted));
					$criteria->addOperator($conditionDelimiter);
					$criteria->addCondition(self::build($value));
				}elseif(is_int($key) && is_string($value)){
					$wanted = trim($value,"\r\n\t\0\x0B ");
					list($key, $operator, $wanted, $conditionDelimiter) = preg_split('@\s+@',$wanted);
					if(!$operator) $operator = '=';
					if(!$conditionDelimiter) $conditionDelimiter = 'AND';
					$criteria->addCondition(new DataMapCriteria($key,$operator,$wanted));
					$criteria->addOperator($conditionDelimiter);
				}else{
					throw new \LogicException('Criteria definition is invalid');
				}
			}
			return $criteria->complete();
		}elseif(is_string($definition)){
			$criteria = new DataMapCriteriaBlock();
			$definition = trim($definition,"()\r\n\t\0\x0B");
			$regex = '@ (!)?(
					(\((?:(?>[\(\)]+) | (?R))\)) |
					(\w+[\.\$\&\:\-\w\d]+\(.*?\)) |
					\s*? ([\+\-\*\/\\\$\#\@\%\^\&\:\=\!]+) \s*? |
					\s+? ([\+\-\*\/\\\$\#\@\%\^\&\:\=\!\w]+) \s+ |
					([\*\.\w\$\-\&\:\d]+) |
					TRUE | FALSE | NULL | AND | OR
					)
				@sxmi';

			if(preg_match_all($regex,$definition,$m)){
				$lastCriteria = false;
				while( ($c = array_shift($m[0])) ){
					$c = trim($c);
					if(!$lastCriteria){
						if(substr($c,0,1)==='('){
							try{
								$criteria = self::build($c);
							}catch (\Exception $e){
								throw new \LogicException('Error on block parsing from definition: '.$c. ' message: '.$e->getMessage());
							}
						}else{
							if(!($operator = array_shift($m[0])))throw new \LogicException('Operator not found!');
							$operator = trim($operator);
							if(!($wanted = array_shift($m[0])))throw new \LogicException('Wanted value not found!');
							$wanted = trim($wanted);
							$criteria->addCondition(new DataMapCriteria($c,$operator,$wanted));
						}
						$lastCriteria = true;
					}else{
						$lastCriteria = false;
						$criteria->addOperator($c);
					}
				}
			}
			return $criteria->complete();
		}elseif(!$definition){
			return null;
		}else{

			throw new \LogicException('Invalid definition');

		}

	}

}

/**
 * Class DataMapCriteriaBlock
 */
class DataMapCriteriaBlock implements DataMapCriteriaInterface{

	/**
	 * @var DataMapCriteriaInterface[]
	 */
	protected $conditions = [];


	/**
	 * @param DataMapCriteriaInterface $condition
	 */
	public function addCondition(DataMapCriteriaInterface $condition){
		$this->conditions[] = $condition;
	}

	/**
	 * @param $operator
	 */
	public function addOperator($operator){
		$operator = strtoupper($operator);
		if(!in_array($operator,['AND','OR'],true)){
			throw new \LogicException('IS not valid operator '.$operator);
		}
		$this->conditions[] = $operator;
	}

	/**
	 * @param $item
	 * @param DataMapCollection $collection
	 * @return bool
	 */
	public function __invoke($item, DataMapCollection $collection){
		$conditions = $this->conditions;
		while(($condition = array_shift($conditions))){
			if($condition instanceof DataMapCriteriaInterface){
				if(isset($value)){
					if(isset($operator) && $operator){
						switch($operator){
							case DataMapCriteria::OP_AND:
								$value = $value && call_user_func($condition,$item,$collection);
								break;
							case DataMapCriteria::OP_OR:
								$value = $value || call_user_func($condition,$item,$collection);
								break;
						}
					}else{
						return $value;
					}
				}else{
					$value      = call_user_func($condition,$item,$collection);
					$operator   = array_shift($conditions);
				}
			}
		}

		if(isset($value)){
			return $value;
		}else{
			return true;
		}
	}

	/**
	 * @return $this
	 */
	public function complete(){
		$count = count($this->conditions);
		if($count){
			$lastIndex = $count-1;
			$hasClears = false;
			if(is_string($this->conditions[0])){
				unset($this->conditions[0]);
				$hasClears = true;
			}
			if(is_string($this->conditions[$lastIndex])){
				unset($this->conditions[$lastIndex]);
				$hasClears = true;
			}
			if($hasClears){
				ksort($this->conditions);
			}
		}
		return $this;
	}
}

class DataMap{

}

class DataMapGroup{

	/** @var  string */
	protected $name;

	/** @var  DataMap */
	protected $object;

}

class DataMapGrouper{

	/** @var DataMapGroup[]  */
	protected $groups = [];


	public function compare($data_map){

	}

}

class DataMapValueAccess{

	/** @var callable[]|DataMapValueAccessGetter[] */
	protected static $setter_collection = [];

	/** @var callable[]|DataMapValueAccessSetter[] */
	protected static $getter_collection = [];

	/**
	 * @return callable|DataMapValueAccessSetter
	 */
	public static function getDefaultSetter(){
		return self::getSetter('default');
	}

	/**
	 * @param $key
	 * @param callable|DataMapValueAccessSetter $setter
	 */
	public static function setSetter($key, callable $setter){
		self::$setter_collection[$key] = $setter;
	}

	/**
	 * @param $key
	 * @return callable|DataMapValueAccessSetter
	 */
	public static function getSetter($key){
		if(!isset(self::$setter_collection[$key])){
			if($key === 'default'){
				self::$setter_collection[$key] = new DataMapValueAccessSetter();
			}else{
				return null;
			}
		}
		return self::$setter_collection[$key];
	}



	/**
	 * @return callable|DataMapValueAccessGetter
	 */
	public static function getDefaultGetter(){
		return self::getGetter('default');
	}

	/**
	 * @param $key
	 * @param callable|DataMapValueAccessGetter $accessor
	 */
	public static function setGetter($key, callable $accessor){
		self::$getter_collection[$key] = $accessor;
	}

	/**
	 * @param $key
	 * @return callable|DataMapValueAccessGetter
	 */
	public static function getGetter($key){
		if(!isset(self::$getter_collection[$key])){
			if($key === 'default'){
				self::$getter_collection[$key] = new DataMapValueAccessGetter();
			}else{
				return null;
			}
		}
		return self::$getter_collection[$key];
	}

	/**
	 * @param callable|DataMapValueAccessGetter|string|null $getter
	 * @return callable|DataMapValueAccessGetter|null
	 */
	public static function checkoutGetter($getter = null){
		if(is_object($getter) && !$getter instanceof \Closure && !$getter instanceof DataMapValueAccessGetter){
			throw new \LogicException('GETTER is not valid getter object (not instanceof \DataMapValueAccessGetter)');
		}
		if(is_string($getter)){
			$s = $getter;
			$getter = DataMapValueAccess::getGetter($getter);
			if(!$getter){
				throw new \LogicException('Not found getter by key "'.$s.'"');
			}
		}elseif(!is_callable($getter)){
			throw new \InvalidArgumentException('Invalid Getter');
		}
		return $getter;
	}

	/**
	 * @param callable|DataMapValueAccessSetter|string|null $setter
	 * @return callable|DataMapValueAccessSetter|null
	 */
	public static function checkoutSetter($setter = null){
		if(is_object($setter) && !$setter instanceof \Closure && !$setter instanceof DataMapValueAccessSetter){
			throw new \LogicException('SETTER is not valid setter object (not instanceof \DataMapValueAccessSetter)');
		}
		if(is_string($setter)){
			$s = $setter;
			$setter = DataMapValueAccess::getSetter($setter);
			if(!$setter){
				throw new \LogicException('Not found setter by key "'.$s.'"');
			}
		}elseif(!is_callable($setter)){
			throw new \InvalidArgumentException('Invalid Setter');
		}
		return $setter;
	}



}


class DataMapValueAccessSetter{

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function __invoke($data, $key, $value){
		if(is_object($data)){
			$this->setToObject($data,$key,$value);
		}elseif(is_array($data)){
			$this->setToArray($data,$key,$value);
		}
		return $data;
	}

	/**
	 * @param $object
	 * @param $key
	 * @param $value
	 */
	protected function setToObject(& $object, $key, $value){
		$object->{$key} = $value;
	}

	/**
	 * @param $array
	 * @param $key
	 * @param $value
	 */
	protected function setToArray(& $array, $key, $value){
		$array[$key] = $value;
	}

}

/**
 * Class DataMapValueAccessGetter
 */
class DataMapValueAccessGetter{

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function __invoke($data, $key){
		if(is_object($data)){
			return $this->getFromObject($data,$key);
		}elseif(is_array($data)){
			return $this->getFromArray($data,$key);
		}else{
			return null;
		}
	}

	/**
	 * @param $object
	 * @param $key
	 * @return mixed
	 */
	protected function getFromObject($object, $key){
		if(isset($object->{$key})){
			return $object->{$key};
		}
		return null;
	}

	/**
	 * @param $array
	 * @param $key
	 * @return mixed
	 */
	protected function getFromArray($array, $key){
		if(isset($array[$key])){
			return $array[$key];
		}
		return null;
	}

}

/**
 * DataMapCollection может обрабатывать разные типы наборов данных
 */

class ObjectTest{
	protected $id;

	protected $name;

	protected $city;

	public function __construct($id,$name,$city=null){$this->id = $id;$this->name = $name;$this->city = $city;}
	public function getId(){return $this->id;}
	public function getName(){return $this->name;}
	public function getCity(){return $this->city;}

}
$data_map_collections_test = [

	'arrays' => [
		[1,'Alex','Khabarovsk'],
		[2,'Valentin','Khabarovsk'],
		[3,'Valeria','Moscow'],
	],

	'arrays_assoc_mapped' => [

		[
			'id'    => 1,
			'name'  => 'Alex',
			'city'  => 'Khabarovsk'
		], [
			'id'    => 2,
			'name'  => 'Valentin',
			'city'  => 'Khabarovsk'
		], [
			'id'    => 3,
			'name'  => 'Valeria',
			'city'  => 'Moscow'
		],


	],

	'instances' => [

		new ObjectTest(1,'Alex','Khabarovsk'),
		new ObjectTest(2,'Valentin','Khabarovsk'),
		new ObjectTest(3,'Valeria','Moscow'),

	]


];


/**
 * Class ColumnAccessor
 */
class ColumnAccessor{

	/** @var array [Alias] => [Key] */
	protected $column_map = [];

	/** @var callable[] */
	protected static $accessor_collection = [];

	/**
	 * @param $key
	 * @param callable $accessor
	 */
	public static function setAccessor($key, callable $accessor){
		self::$accessor_collection[$key] = $accessor;
	}

	/**
	 * @return null|callable|ColumnAccessor
	 */
	public static function getDefaultAccessor(){
		return self::getAccessor('default');
	}

	/**
	 * @param $key
	 * @return null|callable|ColumnAccessor
	 */
	public static function getAccessor($key){
		if(isset(self::$accessor_collection[$key])){
			return self::$accessor_collection[$key];
		}else if($key === 'default'){
			self::$accessor_collection[$key] = new ColumnAccessor();
			return self::$accessor_collection[$key];
		}else{
			return null;
		}
	}

	/**
	 * @param $map
	 */
	public function __construct($map = null){
		if($map){
			$this->setColumnMap($map);
		}
	}

	/**
	 * @param $key
	 * @param $alias
	 * @return $this
	 */
	public function setColumnKey($alias,$key){
		$this->column_map[$alias] = $key;
		return $this;
	}

	/**
	 * @param $alias
	 * @return mixed
	 */
	public function getColumnKey($alias){
		return isset($this->column_map[$alias])?$this->column_map[$alias]:$alias;
	}

	/**
	 * @param array $map
	 * @return $this
	 */
	public function setColumnMap(array $map){
		$this->column_map = array_flip($map);
		return $this;
	}

	/**
	 * @param $data
	 * @param $alias
	 * @param Sorter $sorter
	 * @return null|mixed
	 */
	public function __invoke($data, $alias, Sorter $sorter){
		if($alias === null){
			return $data;
		}
		$key = $this->getColumnKey($alias);
		if($key === null){
			return $data;
		}
		if(is_object($data)){
			return $this->getFromObject($data,$key);
		}elseif(is_array($data)){
			return $this->getFromArray($data,$key);
		}else{
			return null;
		}
	}

	/**
	 * @param $object
	 * @param $key
	 * @return mixed
	 */
	protected function getFromObject($object, $key){
		if(isset($object->{$key})){
			return $object->{$key};
		}
		return null;
	}

	/**
	 * @param $array
	 * @param $key
	 * @return mixed
	 */
	protected function getFromArray($array, $key){
		if(isset($array[$key])){
			return $array[$key];
		}
		return null;
	}


}
/*
$sorter = new DataMapCollectionSorter();

$sorter->setColumnAccess(new ColumnAccessor([
	'id', 'name' , 'description'
]));
$sorter->setOrderBy('name','ASC');
$data_map = [
	[1,'Semen','Serenadist'],
	[2,'Sergey','Scandalist'],
	[3,'Ivan','Bulbulatorist'],
	[4,'Petr','Evdakimist'],
	[5,'Alexey','Pacific'],
	[6,'Roman','Specialist'],
	[7,'Alexander','Fasolist'],
	[8,'Pasha','Falconist'],
	[9,'Lesha','BlaBla'],
];

echo '<div>';

echo '<div style="display:inline-block;">Initial: ';
foreach($data_map as $m){
	echo '<p>['.implode(',',array_map(function($m){return var_export($m,true);},$m)).']</p>';
}
echo '</div>';
$aliases = ['id','name','description'];
foreach($aliases as $cName){
	$sorter->setOrderBy($cName,null,$aliases);
	$sorter->sort($data_map);
	echo '<div style="display:inline-block;">Order By '.$cName.': ';
	foreach($data_map as $m){
		echo '<p>['.implode(',',array_map(function($m){return var_export($m,true);},$m)).']</p>';
	}
	echo '</div>';
}
echo '</div>';

$source = [1,'Name','Petr'];
*/


$data_map = [
	[1,'Semen','Serenadist'],
	[2,'Sergey','Scandalist'],
	[3,'Ivan','Bulbulatorist'],
	[4,'Petr','Evdakimist'],
	[5,'Alexey','Pacific'],
	[6,'Roman','Specialist'],
	[7,'Alexander','Fasolist'],
	[8,'Pasha','Falconist'],
	[9,'Lesha','BlaBla'],
];

class C{
	protected $id;
	protected $name;
	protected $description;

	public function __construct($id,$name,$description){
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
	}

	public function getId(){return $this->id;}
	public function getName(){return $this->name;}
	public function getDescription(){return $this->description;}

}

$collection = new DataMapCollection(
	(new DataMapSchema())->setFields([
		(new DataMapSchemaField('id'))->setGetter(function(C $data){return $data->getId();}),
		(new DataMapSchemaField('name'))->setGetter(function(C $data){return $data->getName();}),
		(new DataMapSchemaField('description'))->setGetter(function(C $data){return $data->getDescription();}),
	])->setDataConverter(function($data){
		list($id,$name,$description) = $data;
		return new C($id,$name,$description);
	}),
	$data_map
);
$collection->sortBy(null);

echo '<div>';

echo '<div style="display:inline-block;">Initial: ';

echo '<p>['.implode(',',array_map(function($_){return '<b style="color:deepskyblue">'.$_.'</b>';},$collection->getFieldNames())).']</p>';
foreach($collection as $item){
	echo '<p>['.implode(',',array_map(function($_){return '<i style="color:orangered">'.var_export($_,true).'</i>';},$collection->getRow($item))).']</p>';
}
echo '</div>';
foreach($collection->getSchema()->getFields() as $field){
	$collection->sortBy($field->getName());
	echo '<div style="display:inline-block;">Order By '.$field->getName().': ';
	echo '<p>['.implode(',',array_map(function($_){return '<b style="color:deepskyblue">'.$_.'</b>';},$collection->getFieldNames())).']</p>';
	foreach($collection as $item){
		echo '<p>['.implode(',',array_map(function($_){return '<i style="color:orangered">'.var_export($_,true).'</i>';},$collection->getRow($item))).']</p>';
	}
	echo '</div>';
}
echo '</div>';




$criterias = [
	'name start-with S',
	'description start-with S'
];

foreach($criterias as $criteria){
	$c = DataMapCriteria::build($criteria);
	$collected = $collection->collect($c);
	echo '<div style="display:inline-block;vertical-align: top">Collected with criteria <br/><b style="color:#483D8B;font-size:18px;">'.$criteria.'</b>: ';
	echo '<p>['.implode(',',array_map(function($_){return '<b style="color:deepskyblue">'.$_.'</b>';},$collection->getFieldNames())).']</p>';
	foreach($collected as $item){

		echo '<p>['.implode(',',array_map(function($_){return '<i style="color:orangered">'.var_export($_,true).'</i>';},$collection->getRow($item))).']</p>';

	}
	echo '</div>';

}

