<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 2:21
 */
namespace modelX;


include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';





abstract class SchemaOverlay extends SchemaOuterInteraction{

	/** @var  SchemaOuterInteraction */
	protected $ancestor_schema;

	/** @var  ValueAccessor */
	protected $value_accessor;

	/**
	 * @param $data
	 * @return ValueAccessor
	 */
	public function getValueAccessor($data){
		if(!$this->value_accessor){
			$this->value_accessor = new ValueAccessor();
			$this->value_accessor->setAccessor($this->ancestor_schema);
		}
		$this->value_accessor->setData($data);
		return $this->value_accessor;
	}

}

/**
 * Class FieldOverlay
 * @package modelX
 *
 * @property SchemaOverlay $schema
 */
abstract class FieldOverlay extends Field implements OuterValueAccessAwareInterface{

	/** @var   */
	protected $formula_setter;

	/** @var   */
	protected $formula_getter;

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key){
		return call_user_func($this->formula_getter, $this->getValueAccessor($data), $key);
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function valueAccessSet($data, $key, $value){
		$accessor = $this->getValueAccessor($data);
		$result = call_user_func($this->formula_setter,$accessor , $key);
		if($result){
			return $result;
		}
		return $accessor->getData();
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessExists($key){
		return $key === $this->getName();
	}

	/**
	 * @param $data
	 * @return ValueAccessor
	 */
	protected function getValueAccessor($data){
		$schema = $this->schema;
		return $schema->getValueAccessor($data);
	}


}







/**
 * Interface FieldAliasesAwareInterface
 * @package modelX
 */
interface FieldAliasesAwareInterface{

	/**
	 * @param $field_name
	 * @param $original_key
	 * @param null $getter
	 * @param null $setter
	 * @return mixed
	 */
	public function setFieldAlias($field_name, $original_key=null, $getter = null, $setter = null);

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getFieldAlias($key);

	/**
	 * @param $key
	 * @return OuterGetter
	 */
	public function getFieldGetter($key);

	/**
	 * @param $key
	 * @return OuterSetter
	 */
	public function getFieldSetter($key);

}


abstract class SchemaDecoratorBase extends SchemaOuterInteraction implements SchemaAwareInterface{

	/** @var  Schema */
	protected $schema;
	public function setSchema(Schema $schema){
		$this->schema = $schema;
		return $this;
	}
	public function getSchema(){
		return $this->schema;
	}

	public function getField($name){
		return $this->schema->getField($name);
	}
	public function getFieldIndex($field){
		return $this->schema->getFieldIndex($field);
	}
	public function getFieldByIndex($index){
		return $this->schema->getFieldByIndex($index);
	}
	public function getFieldNames(){
		return $this->schema->getFieldNames();
	}
	public function getFields(){
		return $this->schema->getFields();
	}
	public function getPrimaryFieldName(){
		return $this->schema->getPrimaryFieldName();
	}
	public function getPrimaryField(){
		return $this->schema->getPrimaryField();
	}
	public function isPrimaryField($field){
		return $this->schema->isPrimaryField($field);
	}
	public function isUniqueField($field){
		return $this->schema->isUniqueField($field);
	}
	public function getIndex($name){
		return $this->schema->getIndex($name);
	}
	public function getIndexes(){
		return $this->schema->getIndexes();
	}

}


/**
 * Class SchemaAliased
 * @package modelX
 */
class SchemaAliased extends SchemaDecoratorBase implements FieldAliasesAwareInterface{

	/** @var array */
	protected $access_rules = [];

	/** @var  Schema */
	protected $schema;


	/**
	 * @param $field_name
	 * @param $original_key
	 * @param $getter
	 * @param $setter
	 * @return mixed
	 */
	public function setFieldAlias($field_name, $original_key=null, $getter=null, $setter=null){
		$this->access_rules[$field_name] = [
			'alias' => $original_key,
			'getter' => $getter,
			'setter' => $setter
		];
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrimaryFieldName(){
		$name = $this->schema->getPrimaryFieldName();
		return $this->getFieldAlias($name);
	}

	/**
	 * @return string[]|int[]
	 */
	public function getFieldNames(){
		$aliases = [];
		foreach($this->access_rules as $key => $access){
			$aliases[$access['alias']] = $key;
		}
		$fNames = $this->schema->getFieldNames();
		return array_replace(array_combine($fNames,$fNames), $aliases);
	}

	/**
	 * @param FieldInterface|string $field
	 * @return bool
	 */
	public function isPrimaryField($field){
		return $this->schema->isPrimaryField($this->getFieldAlias($field));
	}

	/**
	 * @param FieldInterface|string $field
	 * @return bool
	 */
	public function isUniqueField($field){
		return $this->schema->isUniqueField($this->getFieldAlias($field));
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getFieldAlias($key){
		if(!isset($this->access_rules[$key]['alias'])){
			return $key;
		}
		return $this->access_rules[$key]['alias'];
	}

	/**
	 * @param $key
	 * @return OuterGetter
	 */
	public function getFieldGetter($key){
		if(!isset($this->access_rules[$key])){
			throw new \LogicException('Field name "'.$key.'" alias not defined!');
		}
		if(!isset($this->access_rules[$key]['getter'])){
			return OuterValueAccess::getDefaultGetter();
		}
		return $this->access_rules[$key]['getter'];
	}

	/**
	 * @param $key
	 * @return OuterSetter
	 */
	public function getFieldSetter($key){
		if(!isset($this->access_rules[$key])){
			throw new \LogicException('Field name "'.$key.'" alias not defined!');
		}
		if(!isset($this->access_rules[$key]['setter'])){
			return OuterValueAccess::getDefaultSetter();
		}
		return $this->access_rules[$key]['setter'];
	}

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key){
		if(!isset($this->access_rules[$key])){
			if($this->schema instanceof SchemaOuterInteraction){
				return $this->schema->valueAccessGet($data, $key);
			}else{
				throw new \LogicException('');
			}
		}
		if(!isset($this->access_rules[$key]['getter'])){
			$getter = OuterValueAccess::getDefaultGetter();
		}else{
			$getter = $this->access_rules[$key]['getter'];
		}
		return OuterValueAccess::handleGetter($getter,$data, $this->access_rules[$key]['alias']?:$key);
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function valueAccessSet($data, $key, $value){
		if(!isset($this->access_rules[$key])){
			if($this->schema instanceof SchemaOuterInteraction){
				return $this->schema->valueAccessSet($data, $key, $value);
			}else{
				throw new \LogicException('');
			}
		}
		if(!isset($this->access_rules[$key]['setter'])){
			$setter = OuterValueAccess::getDefaultSetter();
		}else{
			$setter = $this->access_rules[$key]['setter'];
		}
		return OuterValueAccess::handleSetter($setter,$data, $this->access_rules[$key]['alias']?:$key, $value);
	}

	/**
	 * @param $name
	 * @return FieldOuterInteraction|void
	 * @throws \Exception
	 */
	public function getField($name){
		throw new \Exception(__METHOD__ . ' - NOT EFFECT');
	}

	/**
	 * @param FieldInterface|string $field
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function getFieldIndex($field){
		throw new \Exception(__METHOD__ . ' - NOT EFFECT');
	}

	/**
	 * @param $index
	 * @return FieldInterface|void
	 * @throws \Exception
	 */
	public function getFieldByIndex($index){
		throw new \Exception(__METHOD__ . ' - NOT EFFECT');
	}

	/**
	 * @throws \Exception
	 */
	public function getFields(){
		throw new \Exception(__METHOD__ . ' - NOT EFFECT');
	}

	/**
	 * @throws \Exception
	 */
	public function getPrimaryField(){
		throw new \Exception(__METHOD__ . ' - NOT EFFECT');
	}

	/**
	 * @param $name
	 * @return IndexInterface|null|void
	 * @throws \Exception
	 */
	public function getIndex($name){
		throw new \Exception(__METHOD__ . ' - NOT EFFECT');
	}

	/**
	 * @return IndexInterface[]
	 * @throws \Exception
	 */
	public function getIndexes(){
		throw new \Exception(__METHOD__ . ' - NOT EFFECT');
	}


}





class SmartSchemaTest extends ExampleStage1SchemaOuterInteraction{

	/**
	 * @param FieldInterface[] $decorateFields
	 * @return SmartSchemaTest
	 */
	public function decorate(array $decorateFields){
		$fields = [];
		foreach($this->getFields() as $f){
			$fields[$f->getName()] = $f;
		}
		foreach($decorateFields as $f){
			$fields[$f->getName()] = $f;
		}
		return new self(array_values($fields));
	}

}


$schema = new SmartSchemaTest([

	(new ExampleStage1FieldOuterInteraction('id'))
		->setGetter(function($data,$key){
			return $data[$key];
		})->setSetter(function($data, $key, $value){
			$data[$key] = $value;
			return $data;
		}),

	(new ExampleStage1FieldOuterInteraction('name'))
		->setGetter(function($data, $key){
			return $data[$key];
		})->setSetter(function($data, $key, $value){
			$data[$key] = $value;
			return $data;
		}),

	(new ExampleStage1FieldOuterInteraction('city'))
		->setGetter(function($data,$key){
			return $data[$key];
		})->setSetter(function($data, $key, $value){
			$data[$key] = $value;
			return $data;
		})


],[
	(new ExampleStage1Index('primary_id',IndexInterface::TYPE_PRIMARY))->addField('id'),
	(new ExampleStage1Index('unique_id',IndexInterface::TYPE_UNIQUE))->addField('id')
]);

$schemaDecoration = $schema->decorate([
	(new ExampleStage1FieldOuterInteraction('identifier'))->setOriginalKey('id'),
]);

/**
$decorator = new SchemaAliased();
$decorator->setSchema($schema);
$decorator->setFieldAlias('identifier','id');
$decorator->setFieldAlias('title','name');
*/
$raw_data = [
	'id' => 3,
	'name' => 'Petr',
	'city' => 'Moscow'
];
$data = new DataMap();
$data->setOriginalData($raw_data);


$data->setSchema($schema);
echo '[OuterInteraction InstantSchema] Data Access to `id` property: '.$data->id . '<br/>';

$data->setSchema($schemaDecoration);
echo '[OuterInteraction Decoration] Data Access to `identifier`(`id`) property: '.$data->identifier . '<br/>';

$aliased = new SchemaAliased();
$aliased->setSchema($schema);
$aliased->setFieldAlias('Идентификатор','id');
$data->setSchema($aliased);
echo '[OuterInteraction Aliased] Data Access to `Идентификатор`(`id`) property: '.$data->Идентификатор . '<br/>';

class SimpleSchema extends Schema{

	public function __construct(array $fields, array $indexes = [ ]){
		$this->fields = $fields;
		$this->indexes = $indexes;
	}

}
class SimpleField extends Field{
	public function __construct($name, $type = 'string'){
		$this->name = $name;
		$this->type = $type;
	}
}



/**
 * Class SchemaAccessor
 * @package modelX
 */
class SchemaAccessor extends SchemaDecoratorBase implements FieldAliasesAwareInterface{

	/** @var  array array[OriginalKey, Getter, Setter] */
	protected $access_rules;

	/**
	 * @param $data
	 * @param $key
	 * @return mixed|null
	 */
	public function valueAccessGet($data, $key){
		if(!$this->schema->getField($key)){
			throw new \LogicException('Field name "'.$key.'" alias not defined!');
		}
		if(!isset($this->access_rules[$key])){
			$getter = OuterValueAccess::getDefaultGetter();
			$originalKey = $key;
		}else{
			if(!isset($this->access_rules[$key][1])){
				$getter = OuterValueAccess::getDefaultGetter();
			}else{
				$getter = $this->access_rules[$key][1];
			}
			if(!isset($this->access_rules[$key][0])){
				$originalKey = $key;
			}else{
				$originalKey = $this->access_rules[$key][0];
			}
		}


		return OuterValueAccess::handleGetter($getter,$data,$originalKey);
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $value
	 * @return mixed|null
	 */
	public function valueAccessSet($data, $key, $value){
		if(!$this->schema->getField($key)){
			throw new \LogicException('Field name "'.$key.'" alias not defined!');
		}

		if(!isset($this->access_rules[$key])){
			$getter = OuterValueAccess::getDefaultSetter();
			$originalKey = $key;
		}else{
			if(!isset($this->access_rules[$key][2])){
				$getter = OuterValueAccess::getDefaultSetter();
			}else{
				$getter = $this->access_rules[$key][2];
			}
			if(!isset($this->access_rules[$key][0])){
				$originalKey = $key;
			}else{
				$originalKey = $this->access_rules[$key][0];
			}
		}

		return OuterValueAccess::handleGetter($getter,$data,$originalKey);
	}



	/**
	 * @param $fieldName
	 * @param $originalKey
	 * @param null $getter
	 * @param null $setter
	 * @return $this
	 */
	public function setFieldAlias($fieldName, $originalKey=null, $getter=null,$setter=null){
		$this->access_rules[$fieldName] = [$originalKey,$getter,$setter];
		return $this;
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getFieldOriginalKey($key){
		if(!isset($this->access_rules[$key])){
			return $key;
		}
		return $this->access_rules[$key][0]?:$key;
	}

	/**
	 * @param $key
	 * @return OuterGetter
	 */
	public function getFieldGetter($key){
		if(!isset($this->access_rules[$key])){
			throw new \LogicException('Field name "'.$key.'" alias not defined!');
		}
		if(!isset($this->access_rules[$key][1])){
			return OuterValueAccess::getDefaultGetter();
		}
		return $this->access_rules[$key][1];
	}

	/**
	 * @param $key
	 * @return OuterSetter
	 */
	public function getFieldSetter($key){
		if(!isset($this->access_rules[$key])){
			throw new \LogicException('Field name "'.$key.'" alias not defined!');
		}
		if(!isset($this->access_rules[$key][2])){
			return OuterValueAccess::getDefaultSetter();
		}
		return $this->access_rules[$key][2];
	}


	/**
	 * @param $key
	 * @return mixed
	 */
	public function getFieldAlias($key){
		return $this->access_rules[$key][0];
	}
}

$schema = new SimpleSchema([
	(new SimpleField('id')),
	(new SimpleField('name')),
	(new SimpleField('city'))
]);

$accessor = new SchemaAccessor();
$accessor->setFieldAlias('id',null,function($d){return $d['id'];});

$accessor->setSchema($schema);


$data->setSchema($accessor);

echo '[SimpleSchema Accessor] Data Access to `id` property: '.$data->id . '<br/>';



interface FieldDelegateRuleAware{

	public function setFieldDelegateRule($fieldName, $from, $fromFieldName = null);

}

class SchemaHybrid extends SchemaDecoratorBase implements FieldDelegateRuleAware{

	/** @var  SchemaOuterInteraction[] */
	protected $schemas = [];

	protected $field_rules = [];

	/**
	 * @param string $alias
	 * @param SchemaOuterInteraction $schema
	 * @return $this
	 */
	public function setSchema($alias, $schema){
		$this->schemas[$alias] = $schema;
		return $this;
	}

	/**
	 * @param $fieldName
	 * @param $from
	 * @param null $fromFieldName
	 * @return $this
	 */
	public function setFieldDelegateRule($fieldName, $from, $fromFieldName=null){
		$this->field_rules[$fieldName] = [$from, $fromFieldName];
		return $this;
	}

	/**
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	public function valueAccessGet($data, $key){
		if(!isset($this->field_rules[$key])){}
		$rule = $this->field_rules[$key];
		$schemaAlias = $rule[0];
		$schemaFieldName = $rule[1]?:$key;
		return $this->schemas[$schemaAlias]->valueAccessGet($data, $schemaFieldName);
	}

	public function valueAccessSet($data, $key, $value){
		if(!isset($this->field_rules[$key])){}
		$rule = $this->field_rules[$key];
		$schemaAlias = $rule[0];
		$schemaFieldName = $rule[1]?:$key;
		return $this->schemas[$schemaAlias]->valueAccessSet($data, $schemaFieldName, $value);
	}

}

$schemaHybrid = new SchemaHybrid();

$schemaHybrid->setSchema('user',$accessor);
$schemaHybrid->setSchema('profile',new SmartSchemaTest([
	(new ExampleStage1FieldOuterInteraction('name')),
	(new ExampleStage1FieldOuterInteraction('birth')),
	(new ExampleStage1FieldOuterInteraction('telephone'))
]));

$schemaHybrid->setFieldDelegateRule('id','user');
$schemaHybrid->setFieldDelegateRule('name','user');
$schemaHybrid->setFieldDelegateRule('city','user');
$schemaHybrid->setFieldDelegateRule('nickname','profile','name');
$schemaHybrid->setFieldDelegateRule('birth','profile');
$schemaHybrid->setFieldDelegateRule('telephone','profile');

