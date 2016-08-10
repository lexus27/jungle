<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 2:28
 */
namespace modelX ;
include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';


/** -------------------------------------------------------------
 * ------------------   @Data-View-Experimental   --------------
 * ----------------------------------------------------------------*/

/**
 * Class FrameDataMapIterator
 * @package modelX
 */
class FrameDataMapIterator implements \Iterator{

	protected $index = 0;

	protected $count = 0;

	protected $items;

	/** @var SchemaOuterInteraction  */
	protected $schema;

	/** @var  DataMap */
	protected $data_map_flyweight;

	/**
	 * FrameDataMapIterator constructor.
	 * @param SchemaOuterInteraction $schema
	 * @param array $items
	 */
	public function __construct(SchemaOuterInteraction $schema, array $items){
		$this->items = $items;
		$this->schema = $schema;
	}

	/**
	 * @return SchemaOuterInteraction
	 */
	public function getSchema(){
		return $this->schema;
	}

	/**
	 * @param array $items
	 * @return $this
	 */
	public function setItems(array $items){
		$this->items = $items;
		return $this;
	}


	/**
	 * @return DataMap|ExampleStage1DataMap
	 */
	public function current(){
		$data = $this->items[$this->index];
		if(!$this->data_map_flyweight){
			$this->data_map_flyweight = new ExampleStage1DataMap($this->schema,null);
		}
		$this->data_map_flyweight->setOriginalData($data);
		return $this->data_map_flyweight;
	}

	public function next(){
		$this->index++;
	}

	public function key(){
		return $this->index;
	}

	public function valid(){
		return $this->index < $this->count;
	}

	public function rewind(){
		$this->count  = count($this->items);
		$this->index  = 0;
	}
}

/**
 * Interface XYAxisIteratorRepresentInterface
 * @package modelX
 */
interface XYAxisIteratorRepresentInterface{
	/** @param FrameDataMapIterator $iterator @return string  */
	public function render(FrameDataMapIterator $iterator);
	/** @param DataMap $item @return string */
	public function renderItem(DataMap $item);
	/** @return mixed */
	public function getHeaderField();
	/** @return mixed */
	public function getSchema();
	/** @return DataMap */
	public function getItem();
	/** @return string */
	public function getItemFieldName();
	/** @return string */
	public function getItemFieldValue();
	/** @return string */
	public function before();
	/** @return string */
	public function beforeHeader();
	/** @return string */
	public function beforeHeaderField();
	/** @param Field $field @return string */
	public function renderHeaderField(Field $field);
	/** @return string */
	public function afterHeaderField();
	/** @return string */
	public function afterHeader();
	/** @return string */
	public function beforeEnumeration();
	/** @return string */
	public function beforeItem();
	/** @return string */
	public function beforeItemValue();
	/** @param $value @return string */
	public function renderItemValue($value);
	/** @return string */
	public function afterItemValue();
	/** @return string */
	public function afterItem();
	/** @return string */
	public function afterEnumeration();
	/**@return string */
	public function after();
}

/**
 * Class IteratorRepresent
 * @package modelX
 */
abstract class IteratorRepresent{

	/** @var  Schema */
	protected $schema;

	/** @var  Field[] */
	protected $schema_fields;

	/** @var  Field */
	protected $header_field;

	/** @var  DataMap */
	protected $item;

	protected $item_field_name;

	protected $item_field_value;

	/**
	 * @param FrameDataMapIterator $iterator
	 * @return string
	 */
	public function render(FrameDataMapIterator $iterator){
		$this->schema = $iterator->getSchema();
		$this->schema_fields = $this->schema->getFields();
		$content = '';
		$content.= $this->before();
		$content.= $this->beforeHeader();
		foreach($this->schema_fields as $field){
			$this->header_field = $field;
			$content.= $this->beforeHeaderField();
			$content.= $this->renderHeaderField($field);
			$content.= $this->afterHeaderField();
		}
		$this->header_field = null;
		$content.= $this->afterHeader();
		$content.= $this->beforeEnumeration();
		foreach($iterator as $item){
			$this->item = $item;
			$content.= $this->beforeItem();
			$content.= $this->renderItem($item);
			$content.= $this->afterItem();
		}
		$this->item = null;
		$content.= $this->afterEnumeration();
		return $content;
	}

	/**
	 * @param DataMap $item
	 * @return string
	 */
	public function renderItem(DataMap $item){
		$content = '';
		foreach($item as $property => $value){
			$this->item_field_name = $property;
			$this->item_field_value = $value;
			$content.= $this->beforeItemValue();
			$content.= $this->renderItemValue($value);
			$content.= $this->afterItemValue();
		}
		$this->item_field_name = null;
		$this->item_field_value = null;
		return $content;
	}


	/**
	 * @return mixed
	 */
	public function getHeaderField(){
		return $this->header_field;
	}

	/**
	 * @return mixed
	 */
	public function getSchema(){
		return $this->schema;
	}

	/**
	 * @return string
	 */
	public function before(){}

	/**
	 * @return string
	 */
	public function beforeHeader(){}

	/**
	 * @return string
	 */
	public function beforeHeaderField(){}

	/**
	 * @param Field $field
	 * @return string
	 */
	public function renderHeaderField(Field $field){
		return $field->getName();
	}

	/**
	 * @return string
	 */
	public function afterHeaderField(){}

	/**
	 * @return string
	 */
	public function afterHeader(){}

	/**
	 * @return string
	 */
	public function beforeEnumeration(){}

	/**
	 * @return string
	 */
	public function beforeItem(){}



	/**
	 * @return string
	 */
	public function beforeItemValue(){

	}

	/**
	 * @param $value
	 * @return string
	 */
	public function renderItemValue($value){
		return $value;
	}

	/**
	 * @return string
	 */
	public function afterItemValue(){

	}

	/**
	 * @return string
	 */
	public function afterItem(){}


	/**
	 * @return string
	 */
	public function afterEnumeration(){}


	/**
	 * @return string
	 */
	public function after(){}


}

$schema = new ExampleStage1SchemaOuterInteraction([

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

$items = [
	[
		'id' => 1,
		'name' => 'Petr',
		'city' => 'Moscow'
	], [
		'id' => 2,
		'name' => 'Anna',
		'city' => 'Khabarovsk'
	],[
		'id' => 3,
		'name' => 'Semen',
		'city' => 'Arkhangelsk'
	],[
		'id' => 4,
		'name' => 'Petr',
		'city' => 'Piterburg'
	],[
		'id' => 5,
		'name' => 'Ekaterina',
		'city' => 'Ekaterinburg'
	],[
		'id' => 6,
		'name' => 'Igor',
		'city' => 'New-York'
	],[
		'id' => 7,
		'name' => 'John',
		'city' => 'Florida'
	],[
		'id' => 8,
		'name' => 'Betty',
		'city' => 'Rio Brasilia'
	]
];


/**
 * Class TableIteratorRepresent
 * @package modelX
 */
class TableIteratorRepresent extends IteratorRepresent{
	public $before              = '<table>';
	public $before_header       =   '<thead><tr>';
	public $before_header_field =       '<th>';
	/** @FieldName */
	public $after_header_field  =       '</th>';
	public $after_header        =   '</tr></thead>';
	public $before_enumeration  =   '<tbody>';
	public $before_item         =       '<tr>';
	public $before_item_value   =           '<td>';
	/** @Value */
	public $after_item_value    =           '</td>';
	public $after_item          =       '</tr>';
	public $after_enumeration   =   '</tbody>';
	public $after               = '</table>';

	public function before(){return $this->before;}
	public function beforeHeader(){return $this->before_header;}
	public function beforeHeaderField(){return $this->before_header_field;}
	public function afterHeaderField(){return $this->after_header_field;}
	public function afterHeader(){return $this->after_header;}
	public function beforeEnumeration(){return $this->before_enumeration;}
	public function beforeItem(){return $this->before_item;}
	public function beforeItemValue(){return $this->before_item_value;}
	public function afterItemValue(){return $this->after_item_value;}
	public function afterItem(){return $this->after_item;}
	public function afterEnumeration(){return $this->after_enumeration;}
	public function after(){return $this->after;}
}
$iterator = new FrameDataMapIterator($schema, $items);

$tableRepresentation = new TableIteratorRepresent();
echo $tableRepresentation->render($iterator);
