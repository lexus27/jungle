<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 11:35
 */

namespace Jungle\Smart\Value {

	/**
	 * Class Value
	 * @package Jungle\Smart\Value
	 * Значение которое умеет наследоваться
	 *
	 *
	 * TODO Реализовать определитель значений
	 * rgb(12213,123213,12312)      to      @see Color
	 * 45px                         to      @see Measurement
	 * http://site.ru               to      @see URL
	 * 45.2                         to      @see Float
	 * 45                           to      @see Integer
	 * msdsa                        to      @see String
	 *
	 * TODO Реализовать форматирование данных на выходе
	 * 45000 $                      to      45,000.00 Dollars
	 * 45000 $                      to      Forty-five thousand dollars
	 * TODO Реализовать Language Lexer
	 * 45,000.00 Dollars            to      45,000.00 Долларов|Доллара|Доллар
	 * Forty-five thousand dollars  to      Сорок Пять Тысяч Долларов|Доллара|Доллар
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 */
	class Value implements IValue, IValueSettable, IValueDescendant, IValueExtendable{



		/**
		 * @var null
		 * Статически значение по умолчанию
		 */
		protected static $default_value = null;

		/**
		 * @var Value|null
		 */
		protected $ancestor;

		/**
		 * @var Value[]
		 */

		protected $descendants = [];

		/**
		 * @var mixed|bool
		 */
		protected $value = null;

		/**
		 * @var bool
		 * Указывает на то что было выставленно переопределяющее значение
		 * это значение теперь является базовым и не может наследоваться от предков
		 * тоесть если родительский путь выглядел так:
		 * -> = Extend operation
		 * Base = exhibited value based
		 *
		 * null
		 * <--- Ancestor[1]:exhibited    - because is base
		 * <--- Ancestor[2]:derivative   - extenders = [ Base[1] -> 2*]
		 * <--- Ancestor[3]:derivative   - extenders = [ Base[1] -> 2 -> 3*]
		 * <--- Ancestor[4]:derivative   - extenders = [ Base[1] -> 2 -> 3 -> 4*]
		 *
		 * <--- Descendant[5]:exhibited     - extended, but value is redefined
		 * <--- Descendant[6]:derivative - extenders = [ Base[5] -> 6*]
		 * <--- Descendant[7]:derivative - extenders = [ Base[5] -> 6 -> 7]
		 * <--- Descendant[8]:derivative - extenders = [ Base[5] -> 6 -> 7 -> 8]
		 */
		protected $exhibited = false;

		/**
		 * @var mixed|bool
		 * Наследственный конфигуратор который сейчас выставлен ($this->extender)
		 * уже был применен к текущему объекту
		 */
		protected $extended = false;

		/**
		 * @var bool
		 * Прямо сейчас находится в режиме наследования
		 * это режим в котором производится применение наследственного конфигуратора ($this->extender)
		 */
		protected $extending = false;

		/**
		 * @var callable
		 * Наследственный конфигуратор
		 */
		protected $extender;

		/**
		 * @param bool $value
		 * @param callable $configurator
		 */
		public function __construct($value = null,callable $configurator = null){
			if($value === null){
				$this->value = static::$default_value;
			}else{
				$this->setValue($value);
			}
			$this->apply($configurator);
		}

		/**
		 * @param Value $ancestor
		 * @param bool $appliedInAncestor
		 * @param bool $appliedInOld
		 * @return $this
		 */
		public function setAncestor(
			Value $ancestor     = null,
			$appliedInAncestor  = false,
			$appliedInOld       = false
		){
			$old = $this->ancestor;
			if($old !== $ancestor){
				$this->ancestor = $ancestor;
				$this->refresh();
				if($ancestor && !$appliedInAncestor){
					$ancestor->addDescendant($this, true);
				}
				if($old && !$appliedInOld){
					$old->removeDescendant($this, true);
				}
			}
			return $this;
		}

		/**
		 * @return Value
		 */
		public function getAncestor(){
			return $this->ancestor;
		}

		/**
		 * Сброс exhibited значения
		 */
		public function reset(){
			if($this->exhibited){
				$this->exhibited = false;
				$this->refresh();
			}
		}

		/**
		 * @param $value
		 * @return $this
		 */
		public function setValue($value){

			if($this->beforeValueSet($value)===false){
				return $this;
			}

			if(!$this->compareRaw($value,$this->value)){
				$this->value = $value;
				if(!$this->extending){
					$this->exhibited = true;
				}
				$this->refresh();
			}

			return $this;
		}



		/**
		 * @return mixed
		 */
		public function getValue(){
			return $this->getRaw();
		}

		/**
		 * Выставить наследственный конфигуратор
		 * который вызовется после того как текущему объекту
		 * присвоется значение предка
		 *
		 * @param callable $extender
		 * @return $this
		 *
		 * TODO
		 */
		public function setExtender(callable $extender = null){
			if($this->extender !== $extender){
				$this->extender = $extender;
				$this->refresh();
			}
			return $this;
		}

		/** Создать наследника, и указать наследственный конфигуратор
		 * @param callable $extender
		 * @return $this
		 *
		 * TODO TASK
		 * Реализовать быстрый наследственный конфигуратор в виде строки "string|callable $extender = null"
		 * Строка по типу: ' increment(2) ', 'incrementHUE(20)', 'concat(' hello world')'
		 * Тоесть интерпретация строки в код - который внутри статически преобразуется в callable и используется потом уже из кеша
		 *
		 *
		 * -------------------------------------
		 * $func = Value::extender($extenderKey = 'increment(2)') >>> {
		 *     $extenderKey = trim(strtolower(extenderKey))
		 *      if(!isset(self::$extenders[$extenderKey])){
		 *          self::$extenders[$extenderKey] = self::_parseExtenderDefinition(extenderKey)
		 *      }
		 *      return self::$extenders[$extenderKey]
		 * }
		 * $v = new Number(5)
		 * $d = $v->extend($func)
		 * ----------------------------
		 * $v = new Number(5)
		 * $d = $v->extend($extender = 'increment(2)') >>> {
		 *      $extender = Value::extender($extender)
		 *      return ~VALUE::EXTEND METHOD AFTER~
		 * };
		 *
		 *
		 *
		 */
		public function extend(callable $extender = null){
			if(!$this->extending){
				$descendant = new static();
				$descendant->setAncestor($this);
				if($extender){
					$descendant->setExtender($extender);
				}
				$this->onDelivery($descendant);
				return $descendant;
			}else{
				throw new \LogicException('could not be _call extend in extender!!');
			}
		}



		/** Применить конфигуратор к текущему обьекту
		 * @param callable $configurator
		 * @return $this
		 */
		public function apply(callable $configurator = null){
			if($configurator){
				if($configurator instanceof \Closure){
					$configurator = $configurator->bindTo($this);
				}
				call_user_func($configurator,$this);
			}
			return $this;
		}

		/**
		 * @param IValue|mixed $value
		 * @return bool
		 */
		public function equal($value){
			if($value instanceof IValue){
				return $value === $this || $value->getValue() === $this->getValue();
			}else{
				return $this->getValue() === $value;
			}
		}

		/**
		 * @return string
		 */
		public function __toString(){
			try{
				return (string)$this->getValue();
			}catch(\Exception $e){
				return trigger_error($e,E_USER_ERROR);
			}
		}

		/**
		 * @return null
		 */
		public static function getDefaultRawValue(){
			return static::$default_value;
		}

		/** Обновляем состояние наследственности
		 * @return $this
		 */
		protected function refresh(){
			if(!$this->exhibited && $this->extended && $this->ancestor && !$this->extending){
				$this->value    = static::getDefaultRawValue();
				$this->extended = false;
				$this->onExtendedReset();
			}
			foreach($this->descendants as $descendant){
				$descendant->refresh();
			}
			return $this;
		}

		/** Вызываем сырое значение, включая наследственность
		 * @return mixed
		 */
		protected function getRaw(){
			return $this->checkout()->value;
		}

		/**
		 * @return $this
		 */
		protected function checkout(){
			if(!$this->exhibited && !$this->extending && !$this->extended && $this->ancestor){

				$this->value      = $this->ancestor->getRaw();

				$this->extending  = true;
				$this->beforeExtenderCall();
				$this->apply($this->extender);
				$this->afterExtenderCall();
				$this->extending  = false;

				$this->extended   = true;

			}
			return $this;
		}

		/**
		 * @param $rawValue
		 * @return $this
		 */
		protected function setRaw($rawValue){
			$this->value = $rawValue;
			return $this;
		}

		/**
		 * @param $raw1
		 * @param $raw2
		 * @return bool
		 */
		protected function compareRaw($raw1, $raw2){
			return $raw1 === $raw2;
		}

		/**
		 * @param Value $descendant
		 * @param bool $appliedInDescendant
		 * @return $this
		 */
		protected function addDescendant(Value $descendant, $appliedInDescendant = false){
			$i = $this->searchDescendant($descendant);
			if($i === false){
				$this->descendants[] = $descendant;
				if(!$appliedInDescendant){
					$descendant->setAncestor($this, true);
				}
			}
			return $this;
		}

		/**
		 * @param Value $descendant
		 * @return mixed
		 */
		protected function searchDescendant(Value $descendant){
			return array_search($descendant, $this->descendants, true);
		}

		/**
		 * @param Value $descendant
		 * @param bool $appliedInDescendant
		 * @return $this
		 */
		protected function removeDescendant(Value $descendant, $appliedInDescendant = false){
			$i = $this->searchDescendant($descendant);
			if($i !== false){
				array_splice($this->descendants, $i, 1);
				if(!$appliedInDescendant){
					$descendant->setAncestor(null, false, true);
				}
			}
			return $this;
		}




		/**
		 *
		 * @param $value
		 *
		 *
		 */
		protected function beforeValueSet(& $value){}

		/**
		 * @param Value|static $descendant
		 *
		 * Событие вызывается для ново-созданного потомка
		 * после использования на нем
		 * $descendant->setAncestor($this)
		 * $descendant->setExtender(callable $extender)
		 */
		protected function onDelivery($descendant){}

		/**
		 * Событие вызывается сразу после начала активности $this->extending до вызова конфигуратора
		 */
		protected function beforeExtenderCall(){}

		/**
		 * Событие вызывается до конца активности $this->extending после вызова конфигуратора
		 */
		protected function afterExtenderCall(){}

		/**
		 * Событие вызывается после сбрасывания Value->extended в Value->update()
		 */
		protected function onExtendedReset(){}


		/**
		 * @param $name
		 * @param $value
		 */
		public function __set($name, $value){
			if($name === 'raw'){
				if($this->extending){
					$this->setRaw($value);
				}else{
					throw new \BadMethodCallException(
						'Smart.Value.' . $name . '[SET] property accessed only in extender configurator'
					);
				}
			}else{
				throw new \BadMethodCallException(
					'Smart.Value.' . $name . '[SET] property not found'
				);
			}
		}

		/**
		 * @param $name
		 * @return mixed|null
		 */
		public function __get($name){
			if($name === 'raw'){
				if($this->extending){
					return $this->getRaw();
				}else{
					throw new \BadMethodCallException(
						'Smart.Value.' . $name . '[GET] property accessed only in extender configurator'
					);
				}
			}else{
				throw new \BadMethodCallException(
					'Smart.Value.' . $name . '[GET] property not found'
				);
			}
		}

		/**
		 * @Clone
		 */
		public function __clone(){
			if($this->ancestor){
				$this->ancestor->addDescendant($this);
			}
			$this->descendants = [];
			$this->refresh();
		}

	}
}