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
			$param_name = isset($m[1]) && $m[1] && isset($m[1][$entry_index]) && $m[1][$entry_index]?$m[1][$entry_index]:$i;
			$type       = isset($m[2]) && $m[2] && isset($m[2][$entry_index]) && $m[2][$entry_index]?$m[2][$entry_index]:'string';
			$pattern    = isset($m[3]) && $m[3] && isset($m[3][$entry_index]) && $m[3][$entry_index]?$m[3][$entry_index]:null;
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
function _compile($pattern){
	$values = [];
	$i = 0;
	return preg_replace_callback(EXAMPLE_PLACEHOLDER_REGEX,function($m) use(&$values,&$i){
		$i++;
		$param_name = isset($m[1]) && $m[1]?$m[1]:$i;
		$type       = isset($m[2]) && $m[2]?$m[2]:'string';
		$regex      = isset($m[3]) && $m[3]?$m[3]:null;
		if(!$regex){
			// Ugly code (without regex type library)
			$regex = getPatternByType($type);
		}
		return '(?<'.$param_name.'>'.$regex.')';
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
		$regex      = isset($m[3]) && $m[3]?$m[3]:null;
		if(!$regex){
			// Ugly code (without regex type library)
			$regex = getPatternByType($type);
		}
		return '(?<'.$param_name.'>'.$regex.')';
	},$pattern);
	return (bool)preg_match('@'.$pattern.'@',$subject);
}


$pattern = '/user/{user_id:int:([1-9]\d*)}/{email:email}';
$subject = '/user/27';
$data = [
	'user_id' => 27,
	'email' => 'my@email.ru'
];
echo generate($data,$pattern) . '<br/>';
echo var_dump(match($subject,$pattern)). '<br/>';