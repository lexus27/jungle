<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.03.2016
 * Time: 21:00
 */

class RegExp{}

class Pattern{}

class Sign{

	protected $name;

	protected $recognizer;

	protected $after_sequence;

	protected $before_sequence;

}

class TokenList{

	protected $sign;

	protected $tokens = [];

}
class Token{

	protected $sign;

	protected $identifier;

	protected $recognized;

}
interface TokenInterface{

	public function getSign();

	public function setSign(Sign $sign);

}


$command_syntax = 'BUILD {QUEUE <hours>|null} <package_name> ([<section_definition>])';

class Depth{

	/** @var */
	protected $open_identifier;

	/** @var */
	protected $close_identifier;

	/** @param $sign */
	public function setOpen($sign){

	}

	/** @param $sign */
	public function setClose($sign){

	}

}


/**
 * @RegExp Парсинг текста игнорируя экранированые двойные кавычки
 * @param $char
 * @return string
 */
function ignore_escaped_quote($quote){
	$regex = '[^"\\\\]*(?:\\\\.[^"\\\\]*)*';
	return '[^'.$quote.'\\\\]*(?:\\\\.[^'.$quote.'\\\\]*)*';
}


/**
 * @RegExp Парсинг с использование открытой скобки и закрытой скобки , контролируя их вложеность
 * @param $open_bracket
 * @param $close_bracket
 * @param $between
 * @return string
 */
function regexp_depth($open_bracket,$close_bracket, $between){
	return '@(?: (?:'.preg_quote($open_bracket,'@').'(?:(?>['.preg_quote($open_bracket,'@').preg_quote($close_bracket,'@').']+) | (?R))'.preg_quote($close_bracket,'@').') | '.addcslashes($between,'@').' )@xsmi';
}



$subject = 'helloo($arg1,$arg2,($arg2 - $arg3),$arg3)';
preg_match_all(regexp_depth('(',')','[^\(\)]+'),$subject,$m);
echo '<pre>';

print_r($m);







/**
 * Class Context
 */
class Context{

	/** @var  string */
	protected $source = '';

	/** @var  int */
	protected $start_pos    = 0;

	/** @var null|int  */
	protected $end_pos      = null;

	protected $position = 0;

	/**
	 * @param $source
	 * @return $this
	 */
	public function setSource($source){
		$this->source = $source;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSource(){
		return $this->source;
	}

	/**
	 * @return int
	 */
	public function getLength(){
		return mb_strlen($this->getSource());
	}

	/**
	 * @param int $start_position
	 * @return $this
	 */
	public function setStart($start_position = 0){
		$this->start_pos = $start_position;
		return $this;
	}

	/**
	 * @param null|int $end_position
	 * @return $this
	 */
	public function setEnd($end_position = null){
		$this->end_pos = $end_position;
		return $this;
	}

	/**
	 * @return string
	 */
	public function read(){
		return substr($this->getSource(),$this->start_pos,$this->end_pos);
	}

	/**
	 * @param $char
	 * @return bool
	 */
	public function nextChar($char){
		$source = $this->read();
		return substr($source,$this->position,1) === $char;
	}

	/**
	 * @param $char
	 * @return bool
	 */
	public function prevChar($char){
		$source = $this->read();
		if($this->position<1){
			return false;
		}
		return substr($source,$this->position-1,1) === $char;
	}

	public function nextText($text){

	}

	public function prevText($text){

	}

}