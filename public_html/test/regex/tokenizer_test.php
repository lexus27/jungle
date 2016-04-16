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
	 * @param int $offset
	 * @param int $len
	 * @return string
	 */
	public function after($len = 1,$offset = 0){
		return substr($this->string,$this->position + $offset,$len);
	}

	/**
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

$string = str_repeat('й(',500).'\\\\\\(';



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
function read_after($string, $position, $len = 1,$offset = 0){
	return substr($string,$position+$offset,$len);
}
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


echo '<h1>Variant 1.1 OO version </h1>';
$tokenizer  = new Tokenizer($string);
$t = microtime(true);
foreach($tokenizer as $position => $token){
	if($token === '('){
		for($backslashes = 0; $tokenizer->before(1,$backslashes) === '\\' ;$backslashes++){}
		if($backslashes){
			echo '<b>'.$position.' BS: '.$backslashes.'</b> '.$token.'<br/>';
		}
	}
}
echo sprintf('%.4F',microtime(true) - $t).'<br/>';





echo '<h1>variant 2 (array, array_shift sequence)</h1>';
$tokens = str_split($string);
while($tokens){
	$token = array_shift($tokens);
}
echo sprintf('%.4F',microtime(true) - $t).'<br/>';




echo '<h1>Variant 3 (char shifting sequence)</h1>';
$t = microtime(true);
while($string){
	$token = shift($string);
}


function pop(&$s){
	$ch = substr($s,-1);
	$s = substr($s,0,-1);
	return $ch;
}
function shift(&$s){
	$ch = substr($s,0,1);
	$s = substr($s,1);
	return $ch;
}
echo sprintf('%.4F',microtime(true) - $t);