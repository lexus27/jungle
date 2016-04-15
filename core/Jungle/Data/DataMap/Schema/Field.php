<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:21
 */
namespace Jungle\Data\DataMap\Schema {

	use Jungle\Basic\INamed;
	use Jungle\Data\DataMap\Schema;
	use Jungle\Data\DataMap\ValueAccess;
	use Jungle\Data\DataMap\ValueAccess\Getter;
	use Jungle\Data\DataMap\ValueAccess\Setter;
	use Jungle\Data\Validator;
	use Jungle\Data\Validator\ValidatorAggregate;
	use Jungle\Data\Validator\ValidatorInterface;

	/**
	 * Class Field
	 */
	class Field implements INamed{

		/**
		 * @var  Schema
		 * Схема в которой состоит это поле
		 */
		protected $schema;

		/**
		 * @var string|int
		 * Название поля
		 */
		protected $name;

		/**
		 * @var
		 * Лексико графическое назание поля
		 */
		protected $lexicon_name;

		/**
		 * @var  string
		 * Тип значения асоциированый с этим полем
		 */
		protected $type;

		/**
		 * @var  bool
		 * Значение может быть NULL
		 */
		protected $nullable = false;

		/**
		 * @var  null|mixed
		 * Значение по умолчанию для этого поля
		 */
		protected $default = null;

		/**
		 * @var   string|int
		 * Доступ к оригинальным данным
		 */
		protected $original_key;

		/**
		 * @var   Setter|callable|null
		 * Сеттер для оригинальных данных
		 * $data:function($data, $key,$value)
		 */
		protected $setter;

		/**
		 * @var   Getter|callable|null
		 * Геттер для оригинальных данных
		 * mixed:function($data, $key)
		 */
		protected $getter;

		/**
		 * @var   bool
		 * Значение доступно только для чтения, setter не работает
		 */
		protected $readonly = false;

		/**
		 * @var   bool
		 * Поле не доступно для публики, это поле не будет отображаться в клиентской среде приложения
		 */
		protected $private  = false;

		/**
		 * @var   ValidatorInterface|callable|null
		 */
		protected $validator;


		/**
		 * @param $name
		 */
		public function __construct($name){
			$this->setName($name);
		}

		/**
		 * @param $lexicon_name
		 * @return $this
		 */
		public function setLexiconName($lexicon_name){
			$this->lexicon_name = $lexicon_name;
			return $this;
		}

		/**
		 * @return int|string
		 */
		public function getLexiconName(){
			return $this->lexicon_name===null?$this->name:$this->lexicon_name;
		}


		/**
		 * @param bool|false $readonly
		 * @return $this
		 */
		public function setReadOnly($readonly = false){
			$this->readonly = $readonly;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isReadOnly(){
			return $this->readonly;
		}


		/**
		 * @param bool|false $private
		 * @return $this
		 */
		public function setPrivate($private = false){
			$this->private = boolval($private);
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isPrivate(){
			return $this->private;
		}



		/**
		 * @param null $default
		 * @return $this
		 */
		public function setDefault($default = null){
			$this->default = $default;
			return $this;
		}

		/**
		 * @return mixed|null
		 */
		public function getDefault(){
			return $this->default;
		}


		/**
		 * @param bool|true $nullable
		 * @return $this
		 */
		public function setNullable($nullable = true){
			$this->nullable = (bool)$nullable;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isNullable(){
			return $this->nullable;
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
			return $this->schema;
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
		 * @return string
		 */
		public function getType(){
			return $this->type;
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

		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}


		/**
		 * @return mixed
		 */
		public function getInternalIndex(){
			return $this->getSchema()->searchField($this);
		}


		/**
		 * @return bool
		 */
		public function isVirtual(){
			return $this->original_key === null;
		}


		/**
		 * @param string|int|callable $key
		 * @return $this
		 */
		public function setOriginalKey($key = null){
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
		 * @return bool
		 */
		public function isPrimary(){

			$this->getSchema();

			foreach($this->getSchema()->getIndexes() as $index){
				if($index->hasField($this->getName()) && $index->getType() === $index::TYPE_PRIMARY){
					return true;
				}
			}
			return false;
		}


		/**
		 * @param callable|Setter|string|null $setter
		 * @return $this
		 */
		public function setSetter($setter = null){
			$this->setter = ValueAccess::checkoutSetter($setter);
			return $this;
		}

		/**
		 * @return callable|Setter|null
		 */
		public function getSetter(){
			return $this->setter;
		}


		/**
		 * @param callable|Getter|string|null $getter
		 * @return $this
		 */
		public function setGetter($getter = null){
			$this->getter = ValueAccess::checkoutGetter($getter);
			return $this;
		}

		/**
		 * @return callable|Getter|null
		 */
		public function getGetter(){
			return $this->getter;
		}


		/**
		 * @param ValidatorInterface|callable|null $validator
		 * @return $this
		 */
		public function setValidator($validator = null){
			$this->validator = Validator::checkoutValidator($validator);
			return $this;
		}

		/**
		 * @return ValidatorInterface|callable|null
		 */
		public function getValidator(){
			return $this->validator;
		}


		/**
		 * @param $value
		 * @return bool
		 */
		public function convertType($value){
			if($this->type!==null){
				settype($value,$this->type);
			}
			return $value;
		}

		/**
		 * @param $value
		 * @return bool|array
		 */
		public function validate($value){
			if($this->validator){
				return ValidatorAggregate::decompositeErrors(
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
		public function callGetter($data,callable $default_getter = null){
			$getter = $this->getGetter();
			if(!$getter){
				$getter = $default_getter?ValueAccess::checkoutGetter($default_getter):ValueAccess::getDefaultGetter();
			}
			return call_user_func($getter,$data,$this->getOriginalKey());
		}

		/**
		 * @param $data
		 * @param $value
		 * @param callable $default_setter
		 * @return mixed data set
		 */
		public function callSetter($data,$value,callable $default_setter = null){
			$setter = $this->getSetter();
			if(!$setter){
				$setter = $default_setter?ValueAccess::checkoutSetter($default_setter):ValueAccess::getDefaultSetter();
			}
			return call_user_func($setter,$data,$this->getOriginalKey(),$this->convertType($value));

		}

	}

}

