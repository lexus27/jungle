<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 15:24
 */
namespace Jungle\Util\Value {

	use Jungle\DataOldRefactoring\Collection\Cmp;
	use Jungle\DataOldRefactoring\DataMap\ValueAccess;
	use Jungle\Util\INamed;
	use Jungle\Util\Value\Cmp as UtilCmp;

	/**
	 * Class Massive
	 * @package Jungle\Util
	 */
	class Massive{

		/**
		 * Присутствие
		 */
		const CHECK_TYPE_ISSET          = 'isset';

		/**
		 * Мягкая проверка, если присутствует то проверяет на пустое значение
		 */
		const CHECK_TYPE_EMPTY          = 'empty';

		/**
		 * Строгая проверка, значение должно присутствовать, а далее уже проверка на пустое значение
		 * Данная проверка имеет выраженый эффект в проверке при $modeExists === true
		 */
		const CHECK_TYPE_EMPTY_ISSET     = 'empty-isset';

		protected function __construct(){}

		/**
		 * @param array $array
		 * @param $path
		 * @param $value
		 * @param string $delimiter
		 * @return array
		 */
		public static function setNestedValue(array & $array, $path, $value, $delimiter = '.') {
			$pathParts = explode($delimiter, $path);
			$current = &$array;
			foreach($pathParts as $key){
				if(!is_array($current)){
					$current = [];
				}
				if(!isset($current[$key])){
					$current[$key] = null;
				}
				$current = & $current[$key];
			}
			$current = $value;
			return $array;
		}

		/**
		 * @param array $array
		 * @param $path
		 * @param string $delimiter
		 * @return array
		 */
		public static function getNestedArrayValue(array $array, $path, $delimiter = '.'){
			$pathParts = explode($delimiter, $path);
			$current = &$array;
			foreach($pathParts as $key){
				if(!is_array($current)){
					$current = [];
				}
				if(!isset($current[$key])){
					$current[$key] = null;
				}
				$current = & $current[$key];
			}
			$backup = $current;
			return $backup;
		}

		/**
		 * @param $nestedKey
		 * @param $value
		 * @param string $delimiter
		 * @return mixed
		 */
		public static function initArrayWithNestedElement($nestedKey, $value, $delimiter = '.'){
			if(!is_array($nestedKey)){
				$nestedKey = explode($delimiter, $nestedKey);
			}
			return ($key = array_pop($nestedKey)) ? self::initArrayWithNestedElement($nestedKey, [ $key => $value ],$delimiter) : $value;
		}

		/**
		 * @param $array
		 * @param callable $getter
		 * @param bool $asc
		 * @param callable $collator
		 * @return mixed
		 */
		public static function sortColumn(& $array, callable $getter, $asc = true, callable $collator = null){
			return self::_sortInside($array,$getter,false,$asc,$collator);
		}

		/**
		 * @param $array
		 * @param callable $getter
		 * @param bool|true $asc
		 * @param callable|null $collator
		 * @return mixed
		 */
		public static function keySortColumn(& $array, callable $getter, $asc = true, callable $collator = null){
			return self::_sortInside($array,$getter,true,$asc,$collator);
		}

		/**
		 * @param $array
		 * @param callable $getter
		 * @param bool|false $key_sort
		 * @param bool|true $asc
		 * @param callable|null $valueCmp
		 * @return mixed
		 */
		protected static function _sortInside(
			& $array,
			callable $getter,
			$key_sort = false,
			$asc = true,
			callable $valueCmp = null
		){
			$collator = Cmp::checkoutCmp($valueCmp);
			$getter = ValueAccess::checkoutGetter($getter);
			if($key_sort){
				uksort($array,function($a,$b) use ($getter,$collator, $asc){
					$a = call_user_func($getter,$a);
					$b = call_user_func($getter,$b);
					$result = call_user_func($collator,$a,$b);
					if(!$asc && $result!=0){
						return UtilCmp::invert($result);
					}
					return $result;
				});
			}else{
				usort($array,function($a,$b) use ($getter,$collator, $asc){
					$a = call_user_func($getter,$a);
					$b = call_user_func($getter,$b);
					$result = call_user_func($collator,$a,$b);
					if(!$asc && $result!=0){
						return UtilCmp::invert($result);
					}
					return $result;
				});
			}
		}

		/**
		 * @param $array
		 * @param array $keys
		 * @param bool $caseLess
		 * @return array
		 */
		public static function deleteKeys(array $array,array $keys, $caseLess = false){
			if(!$keys) return $array;
			foreach($array as $key => $_){
				if(($caseLess && Massive::stringExists($keys,$key,$caseLess)) || !in_array($key,$keys,true)){
					unset($array[$key]);
				}
			}
			return $array;
		}

		/**
		 * @param $array
		 * @param array $keys
		 * @param bool $caseLess
		 * @return array
		 */
		public static function leaveKeys(array $array,array $keys, $caseLess = false){
			if(!$keys) return [];
			foreach($array as $key => $_){
				if( ($caseLess && !Massive::stringExists($keys,$key,$caseLess)) || !in_array($key,$keys,true)){
					unset($array[$key]);
				}
			}
			return $array;
		}


		/**
		 * @param INamed[] $array
		 * @param $name
		 * @param callable $cmp
		 * @return INamed|null
		 */
		public static function getNamed(array $array, $name,callable $cmp = null){
			if($cmp===null)$cmp = 'strcmp';
			foreach($array as $item){
				if(
					$item instanceof INamed &&
					call_user_func($cmp,$item->getName(),$name)===0
				){
					return $item;
				}
			}
			return null;
		}

		/**
		 * @param array $array
		 * @param bool|true $caseOnly
		 * @return array
		 */
		public static function getNames(array $array,$caseOnly = true){
			$a = [];
			foreach($array as $item){
				if($item instanceof INamed){
					$a[] = $caseOnly?mb_strtolower($item->getName()):$item->getName();
				}
			}
			return $a;
		}

		/**
		 * @param \Jungle\Util\INamed[] $array
		 * @param $name
		 * @param callable $cmp
		 * @return \Jungle\Util\INamed[]
		 */
		public static function collectNames(array $array, $name,callable $cmp = null){
			if($cmp===null)$cmp = 'strcmp';
			$collection = [];
			foreach($array as $item){
				if(
					$item instanceof INamed &&
					call_user_func($cmp,$item->getName(),$name)===0
				){
					$collection[] = $item;
				}
			}
			return $collection;
		}

		/**
		 * @param INamed[] $array
		 * @param $name
		 * @param callable $cmp
		 * @return \Jungle\Util\INamed[]
		 */
		public static function filterNamed(array $array, $name,callable $cmp = null){
			if($cmp===null)$cmp = 'strcmp';
			$newArray = [];
			foreach($array as $item){
				if($item instanceof INamed){
					if(call_user_func($cmp,$item->getName(),$name)!==0){
						$newArray[] = $item;
					}else{

					}
				}else{
					$newArray[] = $item;
				}
			}
			return $newArray;
		}

		/**
		 * @param INamed[] $array
		 * @param \Jungle\Util\INamed $name
		 * @param callable $cmp
		 * @param null $modeExists
		 */
		public static function setNamed(array & $array, INamed $name,callable  $cmp = null, $modeExists = null){
			if($cmp===null)$cmp = 'strcmp';
			if(!in_array($modeExists,[null,false,true],true)){
				throw new \LogicException('Mode exists is invalid, must be [null|false|true], passed: "'.String::representFrom($modeExists).'" ');
			}
			$exists = [];
			$n = $name->getName();
			foreach($array as $i => $item){
				if($item instanceof INamed){
					if(call_user_func($cmp,$item->getName(),$n)===0){
						$exists[] = $i;
					}
				}
			}
			if($modeExists===true){
				foreach($exists as $i){
					$array[$i] = $name;
				}
			}elseif($modeExists===false){
				if(!$exists){
					$array[] = $name;
				}
			}else{
				$array[] = $name;
			}
		}

		/**
		 * @param \Jungle\Util\INamed[] $array
		 * @param string $name
		 * @param callable $cmp
		 * @return bool|int
		 */
		public static function searchNamed(array $array, $name,callable  $cmp = null){
			if($cmp===null)$cmp = 'strcmp';
			foreach($array as $i => $item){
				if($item instanceof INamed && call_user_func($cmp,$item->getName(),$name)===0){
					return $i;
				}
			}
			return false;
		}

		/**
		 * @param array $destination
		 * @param array $ancestor
		 * @return array
		 */
		public static function extend(array $destination, array $ancestor = []){
			foreach($ancestor as $key => & $item){
				if(!isset($destination[$key])){
					$destination[$key] = $item;
				}
			}
			return $destination;
		}

		/**
		 * Обложение ключей массива по типу $prefix . $key . $suffix
		 * @param array $array
		 * @param string $prefix
		 * @param string $suffix
		 * @return array
		 */
		public static function cover(array $array, $prefix = '',$suffix = ''){
			$a = [];
			$prefix = (string)$prefix;
			$suffix = (string)$suffix;
			foreach($array as $key => & $item){
				$a[$prefix.$key.$suffix] = $item;
			}
			return $a;
		}

		/**
		 * Вычесляет Префикные и Суфиксные ключи в массиве $array и отдает эти элементы в виде массива,
		 * если передан $uncover === true то префиксы и суфиксы будут отчищены из ключей в результирующем массиве
		 * если передан $uncoveredNumericInteger === true то в случае если ключ после снятия ковера
		 * будет проходить проверку is_numeric то произойдет возврат к integer типу ключа.
		 * @param array $array
		 * @param bool $uncover
		 * @param string $prefix
		 * @param string $suffix
		 * @param bool $uncoveredNumericInteger
		 * @return array Covered
		 */
		public static function getCovered(array $array, $uncover = false, $prefix = '',$suffix = '',$uncoveredNumericInteger = false){
			$covered = [];
			$prefix = (string)$prefix;
			$suffix = (string)$suffix;
			foreach($array as $key => & $item){
				if(String::startWith($prefix,$key) && String::endWith($suffix,$key)){
					if($uncover){
						$key = $prefix?String::trimWordsLeft($key,$prefix):$key;
						$key = $suffix?String::trimWordsRight($key,$suffix):$key;
						if($uncoveredNumericInteger && is_numeric($key)){
							$key = intval($key);
						}
					}
					$covered[$key] = $item;
				}
			}
			return $covered;
		}

		/**
		 * Функция аналогична @see getCovered за исключением того что из переданного по ССЫЛКЕ
		 * массива произойдет удаление этих элементов
		 * @param array $array
		 * @param bool|false $uncover
		 * @param string $prefix
		 * @param string $suffix
		 * @param bool $uncoveredNumericInteger
		 * @return array
		 */
		public static function exportCovered(array & $array, $uncover = false, $prefix = '',$suffix = '',$uncoveredNumericInteger = false){
			$covered = [];
			$newArray = [];
			$prefix = (string)$prefix;
			$suffix = (string)$suffix;
			foreach($array as $key => & $item){
				if(String::startWith($prefix,$key) && String::endWith($suffix,$key)){
					if($uncover){
						$key = $prefix?String::trimWordsLeft($key,$prefix):$key;
						$key = $suffix?String::trimWordsRight($key,$suffix):$key;
						if($uncoveredNumericInteger && is_numeric($key)){
							$key = intval($key);
						}
					}
					$covered[$key] = $item;
				}else{
					$newArray[$key] = $item;
				}
			}
			$array = $newArray;
			return $covered;
		}

		/**
		 * Расширеная функция array_map
		 * Которая теперь позволяет менять полностью всю пару $keyPair
		 *
		 * @param array $array
		 *
		 * @param callable $mapper принимает 2 аргумента function mapper($value, & $key){ $key = 'prefix_'.$key;return $v; }
		 * работает аналогично оригинальной функции при возвращенном значении
		 * за исключением того что key и value($valueChangeByReference)
		 * теперь может быть изменен в mapper`е по ссылке,
		 * от функции не будет прочтен возврат(return) значения если $valueChangeByReference === true,
		 *
		 * @param bool $valueChangeByReference - Значение меняется только по ссылке
		 *
		 * Дополнительно: Если Key был изменен по ссылке на NULL,
		 * то функция удаляет такую пару KeyPair из массива
		 *
		 * @return array
		 */
		public static function universalMap(callable $mapper, array $array, $valueChangeByReference = false){
			$a = [];
			foreach($array as $k => $v){
				$key    = & $k;
				$value  = & $v;
				if($valueChangeByReference){
					call_user_func($mapper, $value, $key);
				}else{
					$value = call_user_func($mapper, $value, $key);
				}
				if($key!==null){
					$a[$key] = $value;
				}
			}
			return $a;
		}

		/**
		 * Присваивает элементы по ключам из $items в $destination
		 * @param array & $destination - ссылка на массив к которому нужно применить $items
		 * @param array $items - применяемый к $destination массив элементов
		 * @return array $destination как новый массив
		 */
		public static function apply(array & $destination, array $items = []){
			foreach($items as $key => & $item){
				$destination[$key] = $item;
			}
			return $destination;
		}

		/**
		 * Присваивает элементы по ключам из $items в $destination с проверкой на присутствие в $destination
		 * @param array & $destination - ссылка на массив к которому нужно применить $items
		 * @param array $items - применяемый к $destination массив элементов с которыми произведется проверка каких нет в $destination
		 * @return array $destination как новый массив
		 */
		public static function applyIf(array & $destination, array $items = []){
			foreach($items as $key => & $item){
				if(!isset($destination[$key])){
					$destination[$key] = $item;
				}
			}
			return $destination;
		}

		/**
		 * Приводит все значения массива к типу
		 * @param array $array
		 * @param string $type строка типа @see settype
		 * @return array
		 */
		public static function valuesToType(array $array, $type = 'string'){
			foreach($array as $key => & $item){
				settype($item,$type);
			}
			return $array;
		}

		/**
		 * Преобразует все строковые значения массива в формат CamelCase
		 * @param array $array
		 * @param bool|true $camel
		 * @return array
		 */
		public static function valuesStringCase(array $array, $camel = true){
			foreach($array as $key => & $item){
				if(is_string($item)){
					$item = String::camelCase($item,$camel);
				}
			}
			return $array;
		}
		/**
		 * Преобразует все строковые ключи массива в формат CamelCase
		 * @param array $array
		 * @param bool $camel
		 * @return array новый массив
		 */
		public static function keysCase(array $array, $camel = true){
			$newArray = [];
			foreach($array as $key => & $item){
				if(is_string($key)){
					$newArray[String::camelCase($key,$camel)] = $item;
				}else{
					$newArray[$key] = $item;
				}
			}
			return $newArray;
		}

		/**
		 * Фильтрует пустые значения в массиве
		 * @param array $array
		 * @return array новый массив
		 */
		public static function filterEmpty(array $array){
			$newArray = [];
			foreach($array as $key => & $item){
				if(!empty($item)){
					$newArray[$key] = $item;
				}
			}
			return $newArray;
		}

		/**
		 * Фильтрует все элементы массива у которых ключ является строкой
		 * @param array $array
		 * @return array новый массив
		 */
		public static function filterStringKeys(array $array){
			$newArray = [];
			foreach($array as $key => & $item){
				if(!is_string($key)){
					$newArray[$key] = $item;
				}
			}
			return $newArray;
		}

		/**
		 * Фильтрует все элементы массива у которых ключ является числом
		 * @param array $array
		 * @return array
		 */
		public static function filterIntegerKeys(array $array){
			$newArray = [];
			foreach($array as $key => & $item){
				if(!is_integer($key)){
					$newArray[$key] = $item;
				}
			}
			return $newArray;
		}



		/**
		 * @param array $array
		 * @param bool $full
		 * @return bool
		 */
		public static function isAssoc(array $array, $full = true){
			foreach($array as $i => $v){
				if(is_string($i)){
					if(!$full) return true;
				}elseif($full) return false;
			}
			return true;
		}

		/**
		 * @param array $array
		 * @param bool|true $full
		 * @return bool
		 */
		public static function isIndexed(array $array, $full = true){
			foreach($array as $i => $v){
				if(is_int($i)){
					if(!$full) return true;
				}elseif($full) return false;
			}
			return true;
		}


		/**
		 * @param array $array
		 * @return array
		 */
		public static function exportAssoc(array $array){
			$a = [];
			foreach($array as $k => $val){
				if(is_string($k)){
					$a[$k] = $val;
				}
			}
			return $a;
		}

		/**
		 * @param array $array
		 * @return array
		 */
		public static function exportIndexed(array $array){
			$a = [];
			foreach($array as $k => $val){
				if(is_int($k)){
					$a[$k] = $val;
				}
			}
			return $a;
		}


		/**
		 * Функция Выставляет в массив $array Key-Value если условие $modeExists & $checkType
		 * @param array & $array
		 * @param string|int $key           - Ключ
		 * @param mixed $value              - Значение
		 * @param null|bool $modeExists     - Тип проверки
		 *      true эквивалентно проверки на присутствие, действие произведется если в массиве (существует ИЛИ не пустой) элемент с таким ключем
		 *      false эквивалентно проверке на отсутствие, действие произведется если в массиве (не существует ИЛИ пустой) элемент с таким ключем
		 *      null - эквивалентно отстутствие проверки, действие произведется в любом случае
		 * @param string $checkType (isset|empty)
		 *      Тип проверки:
		 *          isset       === проверка на существование
		 *          empty-isset === проверка на существование и пустое значение
		 *          empty       === проверка на пустое значение
		 * @return array $array
		 */
		public static function &setItem(array & $array,  $key, $value, $modeExists = null, $checkType = self::CHECK_TYPE_ISSET){
			if(!in_array($modeExists,[null,false,true],true)){
				throw new \LogicException('Mode exists is invalid, must be [null|false|true], passed: "'.String::representFrom($modeExists).'" ');
			}
			if($modeExists===false){
				if(
					($checkType === self::CHECK_TYPE_ISSET && !isset($array[$key])) ||
					($checkType === self::CHECK_TYPE_EMPTY && !isset($array[$key]) || empty($array[$key])) ||
					($checkType === self::CHECK_TYPE_EMPTY_ISSET && isset($array[$key]) && empty($array[$key]))
				){
					$array[$key] = $value;
				}
			}elseif($modeExists===true){
				if(
					($checkType === self::CHECK_TYPE_ISSET && isset($array[$key])) ||
					(in_array($checkType,[self::CHECK_TYPE_EMPTY,self::CHECK_TYPE_EMPTY_ISSET]) && isset($array[$key]) && !empty($array[$key]))
				){
					$array[$key] = $value;
				}
			}else{
				$array[$key] = $value;
			}
			return $array;
		}

		/**
		 * @param array         $array      - Источник (KEY=>VALUE)
		 * @param int|string    $key        - Ключ
		 * @param null          $default    - значение по умолчанию которое будет возвращенно из функции,
		 *      если ни в Источнике ни в Источнике умолчаний не был найден элемент по $key
		 * @param array         $defaults   - Источник умолчаний (KEY=>VALUE)
		 * @param null|bool     $modeExists - Тип проверки
		 *      true эквивалентно проверки на присутствие, действие произведется если в массиве (существует ИЛИ не пустой) элемент с таким ключем
		 *      false эквивалентно проверке на отсутствие, действие произведется если в массиве (не существует ИЛИ пустой) элемент с таким ключем
		 *      null - эквивалентно отстутствие проверки, действие произведется в любом случае
		 * @param string $checkType (isset|empty)
		 *      Тип проверки:
		 *          isset === проверка на существование
		 *          empty === проверка на пустое значение
		 * @return array
		 */
		public static function &getItem(array & $array, $key, $default = null, array $defaults = null, $modeExists = null, $checkType = self::CHECK_TYPE_ISSET){
			if(!in_array($modeExists,[null,false,true],true)){
				throw new \LogicException('Mode exists is invalid, must be [null|false|true], passed: "'.String::representFrom($modeExists).'" ');
			}
			if($modeExists===false){
				if(
					($checkType === self::CHECK_TYPE_ISSET && !isset($array[$key])) ||
					($checkType === self::CHECK_TYPE_EMPTY && !isset($array[$key]) || empty($array[$key])) ||
					($checkType === self::CHECK_TYPE_EMPTY_ISSET && isset($array[$key]) && empty($array[$key]))
				){
					return !empty($defaults)?self::getItem($defaults,$key,$default,null, $modeExists, $checkType):$default;
				}
			}elseif($modeExists===true){
				if(
					($checkType === self::CHECK_TYPE_ISSET && isset($array[$key])) ||
					(in_array($checkType,[self::CHECK_TYPE_EMPTY,self::CHECK_TYPE_EMPTY_ISSET]) && isset($array[$key]) && !empty($array[$key]))
				){
					return !empty($defaults)?self::getItem($defaults,$key,$default,null, $modeExists, $checkType):$default;
				}
			}
			return $array[$key];
		}

		/**
		 * Итеративная функция setItem
		 * @param array $array
		 * @param array $items
		 * @param null|bool     $modeExists - Тип проверки
		 *      true эквивалентно проверки на присутствие, действие произведется если в массиве (существует ИЛИ не пустой) элемент с таким ключем
		 *      false эквивалентно проверке на отсутствие, действие произведется если в массиве (не существует ИЛИ пустой) элемент с таким ключем
		 *      null - эквивалентно отстутствие проверки, действие произведется в любом случае
		 * @param string $checkType (isset|empty)
		 *      Тип проверки:
		 *          isset === проверка на существование
		 *          empty === проверка на пустое значение
		 * @return array
		 */
		public static function &setItems(array & $array,array $items, $modeExists = null, $checkType = self::CHECK_TYPE_ISSET){
			if($modeExists===null){
				$array = $items;
			}else{
				foreach($items as $param => $value){
					self::setItem($array,$param,$value,$modeExists,$checkType);
				}
			}
			return $array;
		}

		/**
		 * @param array $array
		 * @param $string
		 * @param bool|false $caseLess
		 * @return bool
		 */
		public static function searchString(array & $array,$string,$caseLess = false){
			foreach($array as $param => $value){
				if(is_string($value) && String::match($string,$value,$caseLess)){
					return $param;
				}
			}
			return false;
		}

		/**
		 * @param array $array
		 * @param $value
		 * @param bool|false $caseLess
		 * @return bool
		 */
		public static function stringExists(array & $array,$value,$caseLess = false){
			foreach($array as $param => $v){
				if(is_string($value) && String::match($value,$v,$caseLess)){
					return true;
				}
			}
			return false;
		}


		/**
		 * @param array $array
		 * @param $key
		 * @return bool
		 */
		public static function keyCaseExists(array & $array,$key){
			foreach($array as $param => $value){
				if(is_string($key) && String::match($key,$param,true)){
					return true;
				}
			}
			return false;
		}

		/**
		 * Вернет индексный массив содержащий значения из $array
		 * соответствующие ключам в $assocKeys с упорядочиванием по порядку $assocKeys
		 * @param array $array
		 * @param array $keys
		 * @param bool $checkIsset
		 * @param null $default
		 * @param bool $allowDefault
		 * @return array
		 */
		public static function orderedKeys(array $array,array $keys, $checkIsset = false, $allowDefault = false, $default = null){
			$a = [];
			foreach($keys as $key){
				if(($checkIsset && isset($array[$key])) || (!$checkIsset && array_key_exists($key,$array))){
					$a[] = $array[$key];
				}elseif($allowDefault){
					$a[] = $default;
				}else{
					throw new \LogicException('Massive::orderedKeys Not found key "'.$key.'" in subject array');
				}
			}
			return $a;
		}

		/**
		 * Вернет индексный массив содержащий значения из $array
		 * соответствующие ключам в $assocKeys с упорядочиванием по порядку $assocKeys
		 * Отличается от @see orderedKeys тем что параметр $assocKeys
		 * может быть ассоциативным массивом что дает возможность использовать значения по умолчанию если их нет в $array
		 * @param array $array
		 * @param array $keys
		 * @param bool|null $skipStrict Если NULL то используется как значение по умолчанию для ключей которых нет в array
		 * Если FALSE то вернет false в случае не находения ключа в $array
		 * TRUE дает характер пропуска
		 * @param bool $checkIsset
		 * @return array
		 */
		public static function orderedAssoc(array $array,array $keys, $skipStrict = false, $checkIsset = false){
			$a = [];
			foreach($keys as $i => $key){
				if(is_string($i)){
					if(($checkIsset && isset($array[$i])) || (!$checkIsset && array_key_exists($i,$array))){
						$a[] = $array[$i];
					}else{
						$a[] = $key;
					}
				}else{
					if(array_key_exists($key,$array)){
						$a[] = $array[$key];
					}elseif($skipStrict===null){
						$a[] = null;
					}elseif($skipStrict===false){
						return false;
					}
				}

			}
			return $a;
		}

		/**
		 * $array = [
		 * 		'module' => 'global'
		 * ];
		 * applyAssocInterface(array $array, array $interface, $skipNotExists = false, $checkIsset = false)
		 * $interface = [
		 * 		'module', 					//strict @see skipStrict
		 * 		'controller', 				//strict @see $skipStrict
		 * 		'action'					//strict @see $skipStrict
		 * ]
		 *
		 * $interface = [
		 * 		'module' => null,			// default if $checkIsset === false
		 * 		'controller' => null,		// default if $checkIsset === false
		 * 		'action' => null 			// default if $checkIsset === false
		 * ]
		 *
		 * $interface = [
		 * 		'module' => null, 			// default if $checkIsset === false
		 * 		'controller' => 'index',	// default
		 * 		'action' => 'index' 		// default
		 * ]
		 *
		 * RESULT = [
		 *
		 *
		 * ]
		 *
		 * @param array $array - Массив
		 * @param array $interface - Интерфейс к которому следует привести массив
		 * @param bool $skipStrict - Если Ложь то выдаст false
		 * @param bool $checkIsset - Если Правда то будет проверять искомый в $array ключ с помощью isset
		 * это значит ключ не будет найден даже если значение равно NULL
		 * @return array|bool
		 */
		public static function applyAssocInterface(array $array, array $interface, $skipStrict = false, $checkIsset = false){
			$strictArray = [];
			foreach($interface as $i => $key){
				if(is_string($i)){
					if(($checkIsset && isset($array[$i])) || (!$checkIsset && array_key_exists($i,$array))){
						$strictArray[$i] = $array[$i];
					}else{
						$strictArray[$i] = $key;
					}
				}else{
					if(array_key_exists($key,$array)){
						$strictArray[$key] = $array[$key];
					}elseif($skipStrict===null){
						$a[$key] = null;
					}elseif($skipStrict===false){
						return false;
					}
				}

			}
			return $strictArray;
		}




	}
}

