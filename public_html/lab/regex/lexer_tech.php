<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.04.2016
 * Time: 20:43
 */


define('EXAMPLE_PLACEHOLDER_REGEX','@\{(\w+)?(?::([\w\s]+))?(?::\(([^)]+)\))?\}@');

function escapeDelimiter($pattern, $delimiter = '@'){
	return addcslashes($pattern,$delimiter);
}
function patternToRegexp($pattern, $modifiers = '', $delimiter = '@'){
	return $delimiter . addcslashes($pattern,$delimiter) . $delimiter . $modifiers;
}
function getMatched($eIndex, $maskIndex, $m, $offset_capture = false, $default = null){
	if(isset($m[$maskIndex]) && $m[$maskIndex]){
		if($offset_capture){
			if(isset($m[$maskIndex][$eIndex]) && $m[$maskIndex][$eIndex][0]){
				return $m[$maskIndex][$eIndex][0];
			}else{
				echo '<pre>' , print_r($m[$maskIndex],1) , '</pre>';
			}
		}else{
			if(isset($m[$maskIndex][$eIndex]) && $m[$maskIndex][$eIndex]){
				echo '<pre>' , print_r($m[$maskIndex][$eIndex],1) , '</pre>';
				return $m[$maskIndex][$eIndex];
			}else{
				echo '<pre>' , print_r($m[$maskIndex],1) , '</pre>';
			}
		}
	}
	echo '<pre>' , print_r($m,1) , '</pre>';
	return $default;
}

function convertType(& $value, $type){

	$aliases = [
		'int'       => 'integer',
		'integer'   => 'integer',
		'double'    => 'double',
		'float'     => 'double',
		'number'    => 'double',
		'string'    => 'string',
		'email'     => 'string'
	];
	settype($value, $aliases[$type]);
}

class Pattern{

	protected static $_pattern_recognizer_regexp = '@\{(\w+)?(?::([\w\s]+))?(?::\(([^)]+)\))?\}@';

	protected $definition;

	protected $compiled_regexp;

	protected $compiled_params = [];


	public function __construct($definition){
		$this->definition = $definition;
	}

	/**
	 *
	 */
	protected function compile(){
		if(!$this->compiled_regexp){
			$this->compiled_regexp = preg_replace_callback(self::$_pattern_recognizer_regexp,function($m){
				$param_name = isset($m[1]) && $m[1]?$m[1]:'';
				$type       = isset($m[2]) && $m[2]?$m[2]:'string';
				$pattern    = isset($m[3]) && $m[3]?$m[3]:null;
				if(!$pattern){
					// Ugly code (without regex type library)
					$pattern = getPatternByType($type);
				}
				$this->compiled_params[$param_name] = [$type, $pattern];
				return '(?<'.$param_name.'>'.$pattern.')';

			},$this->definition);
			if($this->compiled_regexp){
				$this->compiled_regexp = '@'.addcslashes($this->compiled_regexp,'@').'@';
			}
		}
	}

	public function generate($data){
		$this->compile();
		$data = $this->normalize($data);
		return preg_replace_callback(self::$_pattern_recognizer_regexp,function($m) use($data){
			$param_name = isset($m[1]) && $m[1]?$m[1]:'';
			return $data[$param_name];
		},$this->definition);
	}

	public function normalize($data){
		$this->compile();
		foreach($this->compiled_params as $param_name => list($type, $pattern)){
			if(!isset($data[$param_name])){
				throw new \LogicException('Data param is not passed "'.$param_name.'"');
			}
			$t = gettype($data[$param_name]);
			$value = (string) $data[$param_name];
			if(!preg_match('@^'.addcslashes($pattern,'@').'$@',$value,$result)){
				throw new \LogicException('Data invalid value format "'.$param_name.'" = "'.$value.'" '.
				'passed: (type: "'.$t.'", value: "'.$value.'") '.
				'mustbe: (type: "'.$type.'", pattern: "'.$pattern.'")'

				);
			}
			$value = $result[0];
			$data[$param_name] = $value;
		}
		return $data;
	}

	/**
	 * @param $subject
	 * @return array|bool
	 */
	public function match($subject){
		$this->compile();
		$data = [];
		if(preg_match($this->compiled_regexp, $subject, $matches) > 0){
			foreach($this->compiled_params as $param => list($type, $pattern)){
				$value = $matches[$param];
				convertType($value, $type);
				$data[$param] = $value;
			}
			return $data;
		}
		return false;
	}



}

/**
 * @param $data
 * @param $pattern
 * @return mixed
 */
function generate($data,$pattern){
	preg_match_all(EXAMPLE_PLACEHOLDER_REGEX,$pattern,$m);

	if($m){
		$i = 0;

		foreach($m[0] as $entry_index => $matched){
			$param_name = getMatched($entry_index,1,$m,false,$i);
			$type       = getMatched($entry_index,2,$m,false,'string');
			$pattern    = getMatched($entry_index,3,$m,false,null);
			echo '<p/>',$param_name;
			if(!$pattern){
				$pattern = getPatternByType($type);
			}
			if(!isset($data[$param_name])){
				throw new \LogicException('param "'.$param_name.'" not passed');
			}
			$inline_value = (string)$data[$param_name];


			if(!preg_match('@'.$pattern.'@',$inline_value,$m)){
				throw new \LogicException('passed param is not recognized');
			}
			$data[$param_name] = $m[0];
			$i++;
		}

	}

	$i = 0;
	return preg_replace_callback(EXAMPLE_PLACEHOLDER_REGEX,function($m) use($data,&$i){
		$i++;
		$param_name = isset($m[1]) && $m[1]?$m[1]:$i;
		$type       = isset($m[2]) && $m[2]?$m[2]:'string';
		$regex      = isset($m[3]) && $m[3]?$m[3]:null;
		return $data[$param_name];
	},$pattern);
}
/**
 * @param $subject
 * @param $pattern
 * @return bool
 */
function match($subject, $pattern){
	$values = [];
	$count = 0;
	$pattern =  preg_replace_callback(EXAMPLE_PLACEHOLDER_REGEX,function($m) use(&$values,&$count){
		$count++;
		$param_name = isset($m[1]) && $m[1]?$m[1]:$count;
		$type       = isset($m[2]) && $m[2]?$m[2]:'string';
		$pattern    = isset($m[3]) && $m[3]?$m[3]:null;
		if(!$pattern){
			// Ugly code (without regex type library)
			$pattern = getPatternByType($type);
		}
		return '(?<'.$param_name.'>'.$pattern.')';
	},$pattern);
	return preg_match(patternToRegexp($pattern),$subject) > 0;
}

function getPatternByType($type){
	$regex = null;
	switch($type){
		case 'int unsigned':
			$regex = '\d+';
			break;
		case 'int':
			$regex = '[+-]?\d+';
			break;
		case 'float':
			$regex = '[+-]?\d+\.\d*';
			break;
		case 'string':
		case 'any':
			$regex = '.+';
			break;
		case 'word':
			$regex = '\w[\w\d\s]+';
			break;
		case 'email':
			$regex = '\w+(\.\w+)?@\w+[\d\w]*\.\w+[\d\w]*';
			break;
		case 'list':
			$regex = '(?>[^,]+)(?:,(?>[^,]+))*';
	}
	return $regex;
}


$pattern = '/user/{user_id:int:([1-9]\d*)}/{email:email}';
$subject = '/user/27/lexus@mail.ru';
$data = [
	'user_id' => 27,
	'email' => 'my@email.ru'
];


$pattern = new Pattern($pattern);

echo '<pre>';var_dump($pattern->match($subject));echo '</pre>';
echo '<pre>';var_dump($pattern->match($pattern->generate($data)));echo '</pre>';