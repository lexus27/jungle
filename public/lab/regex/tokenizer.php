<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.04.2016
 * Time: 3:31
 */



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


/**
 * Very fastest tokenize, fast than OO variant
 */

/**
 * @param $string
 */
function tokenizer($string){
	$len = strlen($string);
	for($i=0;$i<$len;$i++){
		$token = $string{$i};
		if($token === '('){
			for($backslashes = 0; read_before($string, $i, 1,$backslashes) === '\\' ;$backslashes++){}
			if($backslashes%2==0){

			}
		}elseif($token===')'){
			for($backslashes = 0; read_before($string, $i, 1,$backslashes) === '\\' ;$backslashes++){}
			if($backslashes%2==0){

			}
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