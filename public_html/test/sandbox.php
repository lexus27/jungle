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

echo '<h1>Variant 1 ( select char by pos )</h1>';
$t = microtime(true);
$len = strlen($string);
for($i=0;$i<$len;$i++){
	$token = $string{$i};
	if($token === '('){
		$pos = $i;
		$backslashes = 0;
		while(read_prev($string, $pos, 1) === '\\'){
			$backslashes++;
			$pos--;
		}
		if($backslashes){
			echo '<b>'.$backslashes.'</b><br/>';
		}

	}
}
function read_next($string, $position, $len){
	return substr($string,$position,$len);
}
function read_prev($string, $position, $len){
	$start = $position-$len;
	if($start < 0){
		$len+=$start;
		$start = 0;
	}
	return substr($string,$start,$len);
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