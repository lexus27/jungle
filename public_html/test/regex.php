<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.04.2016
 * Time: 20:24
 */

namespace rgx;
use Jungle\Basic\INamed;


/**
 * Class Token
 * @package router
 *
 * "string"{.+}
 * "int"{\d+}
 *
 *
 *
 * "mash" -> match(int) === false
 *
 */
class Token implements INamed{

	/** @var  string */
	protected $pattern;

	/** @var  string */
	protected $name;

	/**
	 * @param $string
	 * @return bool
	 */
	public function match($string){
		return (bool)preg_match($this->pattern,$string);
	}

	/**
	 * @return string
	 */
	public function getPattern(){
		return $this->pattern;
	}

	/**
	 * Получить имя объекта
	 * @return string
	 */
	public function getName(){
		return $this->name;
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
}
/**
 * Class TokenManager
 * @package router
 */
class TokenManager{

	/** @var  Token[] */
	protected $tokens = [];

	/**
	 * @param $token_name
	 * @param $subject
	 * @return bool
	 */
	public function matchToken($token_name, $subject){
		/** @var Token|null $token */
		$token = Massive::getNamed($this->tokens,$token_name,'strcasecmp');
		if($token){
			return $token->match($subject);
		}
		return false;
	}

}

class DataObject{

	/**
	 * @var Expression
	 */
	protected $_expression;

	protected $_main;

	protected $_data = [];


	public function __construct($matches,$expr){
		$this->_expression = $expr;
		$this->_main = array_shift($matches);

		$this->_data = $matches;

	}

	/**
	 * @return mixed
	 */
	public function getMain(){
		return $this->_main;
	}

	/**
	 * @param $k
	 * @return mixed
	 */
	public function __get($k){
		return $this->_expression instanceof Expression?$this->_expression->access($this->_data, $k):$this->_data[$k];
	}

	/**
	 * @param $k
	 * @return bool
	 */
	public function __isset($k){
		return $this->_expression instanceof Expression?(bool)$this->_expression->access($this->_data, $k)
			:isset($this->_data[$k]);
	}

}
class Expression{

	protected $pattern;

	protected $field_map;

	/**
	 * @param $pattern
	 * @param $field_map
	 */
	public function __construct($pattern,array $field_map){
		$this->pattern      = $pattern;
		$this->field_map    = $field_map;
	}

	/**
	 * @param $data
	 * @param $field
	 * @return mixed
	 */
	public function access($data, $field){
		$i = array_search($field,$this->field_map,true);
		if($i === false){
			return isset($data[$field])?$data[$field]:null;
		}else{
			return isset($data[$i])?$data[$i]:null;
		}
	}

}
class Regex{

	/**
	 * @param $subject
	 * @param $expression
	 * @return bool
	 */
	public function match($subject, $expression){
		return (bool)preg_match($expression, $subject);
	}

	/**
	 * @param $subject
	 * @param $expression
	 * @param array $fields
	 * @return false|DataObject
	 */
	public function matchFirst($subject, $expression,array $fields = []){
		if(preg_match($expression, $subject, $matches)){
			$expr = new Expression($expression,$fields);
			return new DataObject($matches,$expr);
		}else{
			return false;
		}
	}

	/**
	 * @param $subject
	 * @param $expression
	 * @param array $fields
	 * @return false|DataObject[]
	 */
	public function matchAll($subject, $expression,array $fields = []){
		if(preg_match_all($expression, $subject, $matches)){
			$expr = new Expression($expression,$fields);
			$collection = [];
			foreach($matches[0] as $i => $match){
				$data = [];
				foreach($matches as $maskIndex => $mask){
					$data[$maskIndex] = $mask[$i];
				}
				$collection[] = new DataObject($data,$expr);
			}
			return $collection;
		}
		return false;
	}

	/**
	 * @param $subject
	 * @param callable $replacement
	 * @param $expression
	 * @return string
	 */
	public function replaceCallback($subject, callable $replacement, $expression){
		//return preg_replace_callback($expression, )
	}

	public function replace($subject, $replacement, $expression){
		if(is_callable($replacement)){
			return $this->replaceCallback($subject, $replacement, $expression);
		}



		return preg_replace($expression, $replacement,$subject);

	}

	public function split($expression){

	}


	/**
	 * @param $matches
	 * @return array
	 */
	public static function _normalizeMatchesCollection($matches){
		$collection = [];
		foreach($matches as $mask_index => $compliant){
			$collection[] = [];
			foreach($compliant as $entry_index => $value){
				$collection[$entry_index][$mask_index] = $value;
			}
		}
		return $collection;
	}

}

$regex = new Regex;

$collection = $regex->matchAll('<ivan:http://google.com>, <alisa:http://vk.com>, <anna:http://mail.ru>,
<alexey:http://jungle.com>, <ifrosiy>','@<(\w+)(?:\:(.+?))>@',[
	'name', 'address'
]);
foreach($collection as $d){
	echo $d->address . "<br/>";
}
$data = $regex->matchFirst('<ivan:http://google.com>, <alisa:http://vk.com>, <anna:http://mail.ru>,
<alexey:http://jungle.com>','@<(\w+)\:(.+?)>@',[
	'name', 'address'
]);
echo $data->address . "<br/>";




/**
preg_match_all('@\[(\w)\]@','[a][s][d][f][g]',$matches);
echo '<pre>';
var_dump($matches);


*/