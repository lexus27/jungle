<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.03.2016
 * Time: 23:16
 */
use Jungle\DataOldRefactoring\DataMap\Schema\Field;


/**
 *
 * Компонент DataMap является абстракцией над примитивными объектами "data",
 * эти оригинальные данные могут быть представлены в нескольких вариантах.
 *
 * @Originals
 */

/** Индексный массив */
$data = [1, 'Kristi', 'New York'];
// -- -- -- -- -- -- --
$id     = $data[0];
$name   = $data[1];
$city   = $data[2];
//list read
list($id,$name,$city) = $data;


/** Ассоциативный массив */
$data = [
	'id' => 1,
	'name' => 'Kristi',
	'city' => 'New York'
];
// -- -- -- -- -- -- --
$id     = $data['id'];
$name   = $data['name'];
$city   = $data['city'];



/** Objects | Объектная инкапсуляция */
class GenericData{
	protected $id, $name, $city;
	function getId(){return $this->id;}
	function getName(){return $this->name;}
	function getCity(){return $this->city;}

	function setId($id){return $this->id = $id;}
	function setName($name){return $this->name = $name;}
	function setCity($city){return $this->city = $city;}

	function __construct($id=null,$name=null,$city=null){$this->id=$id;$this->name=$name;$this->city=$city;}

}

$data = new GenericData(1,'Kristi','New York');

$data = new GenericData();
$data->setId(1);
$data->setName('Kristi');
$data->setCity('New York');
// -- -- -- -- -- -- --
$id     = $data->getId();
$name   = $data->getName();
$city   = $data->getCity();



/** Public Object Properties | Объект с публичными свойствами */
$data = new \stdClass();
$data->id   = 1;
$data->name = 'Kristi';
$data->city = 'New York';
//-- -- -- -- -- -- --
$id     = $data->id;
$name   = $data->name;
$city   = $data->city;


/** Mixed Data | Смешаные данные */
$data = [

	'prop_1'    => ['identifier'=>1,'c_id_ip'=>'#DD%DD32423423'],
	'location'  => [
		'latitude'  => 1224234,
		'longitude' => 3242342,
		'city'      => 'New York'
	],
	'person' => new GenericData(1,'Kristy')

];
// -- -- -- -- -- -- -- -- -- --

$id     = $data['prop_1']['identifier'];
$name   = $data['person']->getName();
$city   = $data['location']['city'];


/**
 * Field Map | Карта полей.
 */
$FieldMap = [
	[
		'name'      => 'id',
		'type'      => 'int',
		'original'  => 0,
		'nullable'  => false,
	],[
		'name'      => 'name',
		'type'      => 'string',
		'original'  => [
			'getter' => function($represent, $key){
				return true;
			},
			'setter' => function($represent, $key){
				return false;
			}
		]
	],[
		'name'      => 'city',
		'type'      => 'string',
		'original'  => 2,
	],[

		'name'      => 'id-name-city',
		'type'      => 'string',
		'match'  => function($value){
			return boolval(preg_match('@[\d]+\-[\w\s]+\-[\w\s]+@',$value));
		},
		'read-only' => false, // read-only flag
		'virtual'   => [

			'required'  => ['id','name','city'],// [dependency_fields]

			'getter' => function(DataRepresent $represent,$key){
				// return "{$id}-{$name}-{$city}"
				// $key === 'id-name-city' reference to field name
				return $represent->id.'-'.$represent->name.'-'.$represent->city;
			},

			'setter' => function(DataRepresent $represent,$key,$value){
				// $value === "{$id}-{$name}-{$city}"
				list($id, $name, $city ) = explode('-',$value);
				$represent->id      = $id;
				$represent->name    = $name;
				$represent->city    = $city;

				return $represent;
			}
		],
	],
	/**
	 * Schema со скалярными данными ясна, нужно продумать парадигму полей представляющие не скалярные типы данных, а
	 * так-же внешние связи.
	 */
	[
		'name'      => 'messages',
		'nullable'  => true,
		'default'   => [],
		'reference' => [
			'collection' => 'PersonMessages',
		]

	]
];
$FieldInterface = [
	/**
	 * @type string
	 * Имя поля
	 */
	'name',

	/**
	 * @type mixed
	 * Значение по умолчанию для поля
	 */
	'default',

	/**
	 * @type bool
	 * Значение может быть NULL
	 */
	'nullable',

	/**
	 * @type bool
	 * Значение не доступно для записи, только чтение
	 */
	'readonly',

	/**
	 * Для поля выжны original, virtual, reference для каждого поля выбирается один тип доступа из этих перечисленых
	 */

	/**
	 * @type int|string|array
	 * Оригинальное поле, Доступ к оригиналу
	 **/
	'original' => [
		/**
		 * @type string
		 * Ключ поля в оригинальном объекте
		 */
		'key',

		/**
		 * @type string|callable|null
		 */
		'getter',

		/**
		 * @type string|callable|null
		 */
		'setter'
	],

	/**
	 * @type array|null
	 * Виртуальное поле, в массиве описывается конфигурация доступа к значению
	 */
	'virtual' => [

		/**
		 * @type array|string
		 * Требуемые поля в текущей схеме для работоспособности этого виртуального поля
		 */
		'required',

		/**
		 * @type string|callable|null
		 */
		'getter',

		/**
		 * @type string|callable|null
		 */
		'setter'

	],

	/**
	 * @type string|array
	 * Агрегация, Ссылка на другую схему
	 */
	'reference' => [

		/**
		 * @type string
		 */
		'schema',

	],
];


/**
 * Schema | Схема
 */
$Scheme = [

	'properties' => $FieldMap,

	/**
	 * Описание оригинала, выше приводились примеры каким может быть оригинал
	 */
	'original' => [
		'validator' => null, // Валидация оригинального объекта данных
	],

	'references' => ['PersonMessages']

];


/**
 * Class Source
 *
 * Источники бывают разные и их может быть не мало
 *      Основные из них:
 *      2 База данных(DataBase) MySQL, PostGreSql, Microsoft Sql Server and them.
 *      3 Различного рода кеши(Cache) Memcache, MongoDb and them.
 *      4 Файловая система(FileSystem)
 *      5 Потоки данных(Communications)
 *      6 Результаты парсинга
 */
class Source{}



/**
 *
 * Нужно продумать слияние 2х и более схем в 1
 *
 * На примере Базы данных существуют 2 таблицы и 2 схемы
 *
 *  [ `id` , `name`,        `city`      ]       [   `id`,   `telephone`,        `created_on` ]
 *      1    "Vladimir"     "Kalifornia"            1       +1 (2131)23 6       145765321213
 *      .    ..........     ............            .       .............       ............
 *      .    ..........     ............            .       .............       ............
 *      .    ..........     ............            .       .............       ............
 *      .    ..........     ............            .       .............       ............
 *      .    ..........     ............            .       .............       ............
 *
 *
 * Итоговая схема должна быть такой:
 * ['id', 'name', 'city', 'telephone', 'created_on']
 *
 * На уровне базы данных все сводится в построении FOREIGN KEY ключа
 * а при выборке построение запросов либо с JOIN, либо в отложеной инициализации ,
 * при запросе доступа к связаному полю
 *
 */


/**
 * Collection Aggregate
 * Нужно продумать агрегацию данных в Collection
 *
 * представим что у нас есть несколько коллекций
 *
 * Person(One):
 *
 * ['id','name','city']
 *
 * Person Message(Many):
 *
 * ['id','person_id','message']
 * Message.getPerson  = n - 1
 * Person.getMessages = 1 - n
 *
 * В итоге у нас схемы видоизменяются.
 *
 * ['id','name','city','messages'] - ['messages']   - back reference from Message.person_id
 *
 * ['id','person','message']        ['person_id']   - reference aliased as 'person'
 *
 *
 */

/**
 * Interface DataSelfAccessorInterface
 *
 * Связующее звено относящаясе к одиночному объекту данных
 * Схема полей и поведений связывается с оригинальными данными
 *
 * Объект дает функционал доступности единицы объекта через свои магические свойства
 */
interface DataSelfAccessorInterface{

	/**
	 * @param $data
	 * @return $this
	 */
	public function setOriginalData($data);

	/**
	 * @return mixed
	 */
	public function getOriginalData();

	/**
	 * @param \Jungle\DataOldRefactoring\DataMap\Schema $schema
	 * @return $this
	 */
	public function setSchema(\Jungle\DataOldRefactoring\DataMap\Schema $schema);

	/**
	 * @return \Jungle\DataOldRefactoring\DataMap\Schema
	 */
	public function getSchema();

}

/**
 * Class DataSelfAccessor
 */
class DataSelfAccessor implements DataSelfAccessorInterface{

	/** @var  \Jungle\DataOldRefactoring\DataMap\Schema */
	protected $schema;

	/** @var  array|object */
	protected $original_data;


	/**
	 * @param $data
	 * @param \Jungle\DataOldRefactoring\DataMap\Schema|null $schema
	 * @return $this
	 */
	public function setOriginalData($data, \Jungle\DataOldRefactoring\DataMap\Schema $schema = null){
		$this->_setOriginalData($data);
		if($schema!==null){
			$this->setSchema($schema);
		}
		return $this;
	}

	/**
	 * @param $original_data
	 */
	public function _setOriginalData($original_data){
		$this->original_data = $original_data;
	}

	/**
	 * @return array|object
	 */
	public function getOriginalData(){
		return $this->original_data;
	}


	/**
	 * @param \Jungle\DataOldRefactoring\DataMap\Schema $schema
	 * @return $this
	 */
	public function setSchema(\Jungle\DataOldRefactoring\DataMap\Schema $schema){
		$this->schema = $schema;
		return $this;
	}

	/**
	 * @return \Jungle\DataOldRefactoring\DataMap\Schema
	 */
	public function getSchema(){
		return $this->schema;
	}

}

/**
 * Class GetterSelf
 */
class GetterSelf extends DataSelfAccessor{

	/**
	 * @param $key
	 * @return mixed
	 */
	public function __invoke($key){
		return $this->getSchema()->valueAccessGet($this->getOriginalData(),$key);
	}

}

/**
 * Class SetterSelf
 */
class SetterSelf extends DataSelfAccessor{

	/**
	 * @param $key
	 * @param $value
	 */
	public function __invoke($key,$value){
		$this->_setOriginalData(
			$this->getSchema()->valueAccessSet($this->getOriginalData(),$key,$value)
		);
	}

}
/**
 * Class DataRepresent
 */
class DataRepresent extends DataSelfAccessor implements \Iterator, \Countable{

	/** @var  int */
	protected $_i;

	/** @var  array */
	protected $_fields;


	/**
	 * @param \Jungle\DataOldRefactoring\DataMap\Schema $schema
	 * @return $this
	 */
	public function setSchema(\Jungle\DataOldRefactoring\DataMap\Schema $schema){
		if($this->schema !== $schema){
			$this->_i       = null;
			$this->schema   = $schema;
		}
		return $this;
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function get($key){
		return $this->getSchema()->valueAccessGet($this->getOriginalData(),$key);
	}

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function set($key,$value){
		$this->_setOriginalData(
			$this->getSchema()->valueAccessSet($this->getOriginalData(),$key,$value)
		);
		return $this;
	}

	/**
	 * Magic get
	 * @param $key
	 * @return mixed
	 */
	public function __get($key){
		return $this->get($key);
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key,$value){
		$this->set($key,$value);
	}

	/**
	 * @return mixed
	 */
	public function current(){
		return $this->get($this->key());
	}

	/**
	 * @param $value
	 * @return $this
	 */
	public function send($value){
		$this->_setOriginalData(
			$this->getSchema()->valueAccessSet($this->getOriginalData(),$this->key(),$value)
		);
		return $this;
	}

	/**
	 * cursor next
	 */
	public function next(){
		$this->_i++;
	}

	/**
	 * @return string
	 */
	public function key(){
		/** @var Field $field */
		$field = $this->_fields[$this->_i];
		return $field->getName();
	}

	/**
	 * @return bool
	 */
	public function valid(){
		return isset($this->_fields[$this->_i]);
	}

	/**
	 * rewind
	 */
	public function rewind(){
		$this->_initializeIterator();
		$this->_i = 0;
	}

	/**
	 * @return int
	 */
	public function count(){
		$this->_initializeIterator();
		return count($this->_fields);
	}

	/**
	 * _init iterator
	 */
	protected function _initializeIterator(){
		if($this->_i === null){
			$this->_i = 0;
			$this->_fields = $this->getSchema()->getFields();
		}
	}
}




/**
 * FieldMap
 * Требуется добавить возможность шаблонизирования виртуальных полей
 *
 */

/**
 * Фильтрация значений бывает тоже нескольких типов, и может распространяться на прибывшие строки либо внутрение типы
 * значений, но в случае с получением данных от клиента, значения в сыром виде передаются в качестве строки.
 *      Sanitize
 *      Validate
 *      SetType
 */


/** Sanitize Дезинтификация - отчистка от не приемлемых символов */
// digit sanitizing
$original   = '12fs2';
$value      = '122';
// email sanitizing
$original   = 'ma!!!il[][@mail.///ru';
$value      = 'mail@mail.ru';

/** Validate Валидация - проверка значения */
// digit validation
$value = 'as23';    //false
$value = '12px2';   // true
$value = '122';     // true

// email validation
$value   = 'mail@mail.ru';          //true
$value   = 'ma!!!il[][@mail.///ru'; //false

/** SetType Конвертация типа */
//integer conversion
$original   = '12fs2';
$value      = 12;
//boolean conversion
$original   = 'yes';
$original   = '1';
$original   = 1;
$original   = 'on';
$original   = 'ok';
$original   = 'true';
$value = true;

$original   = 'no';
$original   = '0';
$original   = 0;
$original   = 'off';
$original   = '';
$original   = 'false';
$value = false;



