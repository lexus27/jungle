<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.04.2016
 * Time: 17:58
 */

namespace string;
/**
 * Class Tokenizer
 * @package string
 */
class Tokenizer implements \Iterator{

	/** @var  string */
	protected $string;

	/** @var  int */
	protected $position;

	/** @var  int */
	protected $length;

	/**
	 * Tokenizer constructor.
	 * @param $string
	 */
	public function __construct($string){
		$this->length = strlen($string);
		$this->string = $string;
	}

	/**
	 * Считать впереди текущей позиции
	 * @param int $offset
	 * @param int $len
	 * @return string
	 */
	public function after($len = 1,$offset = 0){
		return substr($this->string,$this->position + $offset,$len);
	}

	/**
	 * Считать позади текущей позиции
	 * @param int $offset
	 * @param int $len
	 * @return string
	 */
	public function before($len = 1,$offset = 0){
		$pos  = $this->position - $offset;
		$start = $pos-$len;
		if($start < 0){
			$len+=$start;
			if(!$len)return '';
			$start = 0;
		}
		$e = substr($this->string,$start,$len);
		return $e;
	}

	/**
	 * Проверка впереди
	 * @param $needle
	 * @param bool|false $caseless
	 * @param int $offset
	 * @return bool
	 */
	public function hasAfter($needle, $caseless = false, $offset = 0){
		if(!is_array($needle)){
			$needle = [$needle];
		}
		$ll = null;
		foreach($needle as $item){
			$l = strlen($item);
			if(!isset($s) || $ll != $l){
				$s = $this->after($l,$offset);
				$ll = $l;
			}
			if(($caseless && strcasecmp($s,$item)) || (!$caseless && $s === $item)){
				return true;
			}
		}

		return false;
	}

	/**
	 * Проверка позади
	 * @param $needle
	 * @param bool|false $caseless
	 * @param int $offset
	 * @return bool
	 */
	public function hasBefore($needle, $caseless = false, $offset = 0){
		if(!is_array($needle)){
			$needle = [$needle];
		}
		$ll = null;
		foreach($needle as $item){
			$l = strlen($item);
			if(!isset($s) || $ll != $l){
				$s = $this->before($l,$offset);
				$ll = $l;
			}
			if(($caseless && strcasecmp($s,$item)) || (!$caseless && $s === $item)){
				return true;
			}
		}

		return false;
	}

	/**
	 * @param callable $handler
	 */
	public function handle(callable $handler){
		for($this->position = 0 ; $this->position < $this->length ; $this->position++){
			if(call_user_func($handler,$this,$this->position,$this->length)===false){
				return;
			}
		}
	}


	/**
	 * @return mixed
	 */
	public function current(){
		return $this->string{$this->position};
	}

	/**
	 *
	 */
	public function next(){
		$this->position++;
	}

	/**
	 * @return int
	 */
	public function key(){
		return $this->position;
	}

	/**
	 * @return bool
	 */
	public function valid(){
		return isset($this->string{$this->position});
	}

	/**
	 *
	 */
	public function rewind(){
		$this->position = 0;
	}
}

/**
 * Набор 1500 байтов
 */
$string = str_repeat('й(',500).'\\\\\\(';


/**
 * @Variant
 * 1 Использование побайтовой выборки из источника
 */
echo '<h1>Variant 1.0 ( select char by pos )</h1>';
$t = microtime(true);
$len = strlen($string);
$not_capturing_group_brackets = ['?#','?:','?>','?=','?!','?<=','?<!'];
$opened = [];
$groups_captured = [];
$groups_not_captured = [];
$groups_count = 0;
for($i=0;$i<$len;$i++){
	$token = $string{$i};
	if($token === '('){
		for($backslashes = 0; read_before($string, $i, 1,$backslashes) === '\\' ;$backslashes++){}
		if($backslashes%2==0){
			$capture = !has_after($string, $i,$not_capturing_group_brackets);
			$opened[] = [$i,$capture];
		}
	}elseif($token===')'){
		for($backslashes = 0; read_before($string, $i, 1,$backslashes) === '\\' ;$backslashes++){}
		if($opened){
			list($pos, $capture) = array_shift($opened);
			if($capture){
				$groups_captured[] = [$pos,$i,$groups_count];
			}else{
				$groups_not_captured[] = [$pos,$i,$groups_count];
			}
			$groups_count++;
		}else{
			throw new \LogicException('Error have not expected closed groups!');
		}
	}
}
/**
 * Считывает определенное кол-во байтов начиная с $position + $offset на $len байтов (впереди позиции)
 * @param $string
 * @param $position
 * @param int $len
 * @param int $offset
 * @return string
 */
function read_after($string, $position, $len = 1,$offset = 0){
	return substr($string,$position+$offset,$len);
}

/**
 * Считывает определенное кол-во байтов начиная с $position + $offset на $len байтов (позади позиции)
 * @param $string
 * @param $position
 * @param int $len
 * @param int $offset
 * @return string
 */
function read_before($string, $position, $len = 1,$offset = 0){
	$pos  = $position - $offset;
	$start = $pos-$len;
	if($start < 0){
		$len+=$start;
		if(!$len)return '';
		$start = 0;
	}
	return substr($string,$start,$len);
}

/**
 * Проверяет есть ли позади позиции одна из необходимой последовательности байтов
 * @param $string
 * @param $position
 * @param $needle
 * @param int $offset
 * @return bool
 */
function has_before($string, $position, $needle, $offset=0){
	if(!is_array($needle)){
		$needle = [$needle];
	}
	$ll = null;
	foreach($needle as $item){
		$l = strlen($item);
		if(!isset($s) || $ll != $l){
			$s = read_before($string,$position,$l,$offset);
			$ll = $l;
		}
		if($s === $item) return true;
	}
	return false;
}

/**
 * Проверяет есть ли позади позиции одна из необходимой последовательности байтов
 * @param $string
 * @param $position
 * @param $needle
 * @param int $offset
 * @return bool
 */
function has_after($string, $position, $needle, $offset=0){
	if(!is_array($needle)){
		$needle = [$needle];
	}
	$ll = null;
	foreach($needle as $item){
		$l = strlen($item);
		if(!isset($s) || $ll != $l){
			$s = read_after($string,$position,$l,$offset);
			$ll = $l;
		}
		if($s === $item) return true;
	}
	return false;
}
echo sprintf('%.4F',microtime(true) - $t).'<br/>';

/**
 * @Variant
 * 1.1 Обработка с использованием предыдущего варианта, только в реализованом классе @see Tokenizer
 */
echo '<h1>Variant 1.1 OO version </h1>';
$tokenizer  = new Tokenizer($string);
$t = microtime(true);
$not_capturing_group_brackets = ['?#','?:','?>','?=','?!','?<=','?<!'];
$opened = [];
$groups_captured = [];
$groups_not_captured = [];
$groups_count = 0;
foreach($tokenizer as $position => $token){
	if($token === '('){
		for($backslashes = 0; $tokenizer->before(1,$backslashes) === '\\' ;$backslashes++){}
		if($backslashes%2==0){
			$capture = !$tokenizer->hasAfter($not_capturing_group_brackets);
			$opened[] = [$position,$capture];
		}
	}elseif($token===')'){
		for($backslashes = 0; $tokenizer->before(1,$backslashes) === '\\' ;$backslashes++){}
		if($opened){
			list($pos, $capture) = array_shift($opened);
			if($capture){
				$groups_captured[] = [$pos,$position,$groups_count];
			}else{
				$groups_not_captured[] = [$pos,$position,$groups_count];
			}
			$groups_count++;
		}else{
			throw new \LogicException('Error have not expected closed groups!');
		}
	}
}
echo sprintf('%.4F',microtime(true) - $t).'<br/>';


/**
 * @Variant
 * 2 Обработка с преобразованием в массив, и вытаскиванием каждого первого элемента (байта)
 */
echo '<h1>variant 2 (array, array_shift sequence)</h1>';
$t = microtime(true);
$tokens = str_split($string);
while($tokens){
	$token = array_shift($tokens);
	//bla bla
}
echo sprintf('%.4F',microtime(true) - $t).'<br/>';


/**
 * @Variant
 * 3 Обработка с substr с вытаскиванием каждого первого элемента (байта) (Без преобразования в массив)
 */

echo '<h1>Variant 3 (char shifting sequence)</h1>';
$t = microtime(true);
while($string){
	$token = shift($string);
	//bla bla
}
echo sprintf('%.4F',microtime(true) - $t);


/**
 * Вытаскивает последний байт из строки, модифицируя аргумент $s @see array_pop
 * @param $string
 * @return string
 */
function pop(&$string){
	$b = substr($string,-1);
	$string = substr($string,0,-1);
	return $b;
}

/**
 * Вытаскивает первый байт из строки, модифицируя аргумент $s @see array_shift
 * @param $string
 * @return string
 */
function shift(&$string){
	$b = substr($string,0,1);
	$string = substr($string,1);
	return $b;
}
