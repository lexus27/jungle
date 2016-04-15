<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.04.2016
 * Time: 22:04
 */


/**
 * @param $string
 * @return int
 */
function count_masks($string){
	$elapsed    = '';
	$mask_count = 0;
	$not_capturing_group_brackets = ['?#','?:','?>','?=','?!','?<=','?<!'];
	while($string){
		$token = string_shift($string);
		if($token === '('){
			$count_backslashes = str_get_repeat_end('\\',$elapsed);
			if($count_backslashes%2==0){ // если скобка не экранирована
				if(!start_with_any_of($not_capturing_group_brackets,$string)){ // Не считаем "not capturing
					// groups"
					$mask_count++;
				}
			}

		}
		$elapsed .= $token;
	}
	return $mask_count;
}

/**
 * @param $string
 * @return array
 */
function analyze_groups($string){
	$elapsed    = '';
	$last_token = null;
	$current_pos = 0;
	$opened = [];
	$not_capture_groups = [];
	$capture_groups = [];
	$groups_count = 0;
	$not_capturing_group_brackets = ['?#','?:','?>','?=','?!','?<=','?<!'];
	while($string){
		$token = string_shift($string);
		if($token === '('){
			$count_backslashes = str_get_repeat_end('\\',$elapsed);
			if($count_backslashes%2==0){
				$not_capture = start_with_any_of($not_capturing_group_brackets,$string);
				$opened[] = [$current_pos,!$not_capture];
			}

		}elseif($token === ')'){
			$count_backslashes = str_get_repeat_end('\\',$elapsed);
			if($count_backslashes%2==0){
				if($opened){
					list($pos, $capture) = array_shift($opened);
					if($capture){
						$capture_groups[] = [$pos,$current_pos,$groups_count];
					}else{
						$not_capture_groups[] = [$pos,$current_pos,$groups_count];
					}
					$groups_count++;
				}else{
					throw new \LogicException('Error have not expected closed groups!');
				}
			}
		}
		$current_pos++;
		$elapsed .= $token;
	}
	if($opened){
		throw new \LogicException(
			'Error have not closed opened groups by offset at \'' . implode('\' and \'',array_column($opened,0)).'\''
		);
	}

	return [
		'captured'      => $capture_groups,
		'not_captured'  => $not_capture_groups
	];
}
/**
 * @param string $width
 * @param string $subject
 * @return bool
 */
function start_with($width, $subject){
	return mb_substr($subject,0,mb_strlen($width)) === $width;
}

/**
 * проверка начинается ли $subject одной из строк
 * @param string|string[] $anyOf
 * @param string $subject
 * @return bool
 */
function start_with_any_of($anyOf, $subject){
	if(!is_array($anyOf))$anyOf = [$anyOf];
	$last_l = null;
	foreach($anyOf as $width){
		$l = mb_strlen($width);
		if(!isset($comparable) || $l !== $last_l){
			$last_l = $l;
			$comparable = mb_substr($subject,0,$l);
		}
		if($comparable === $width){
			return true;
		}
	}
	return false;
}

/**
 * проверка заканчивается ли $subject строкой
 * @param string $width
 * @param string $subject
 * @return bool
 */
function end_with($width, $subject){
	return mb_substr($subject,-mb_strlen($width)) === $width;
}

/**
 * проверка заканчивается ли $subject одной из строк
 * @param string|string[] $anyOf
 * @param string $subject
 * @return bool
 */
function end_with_any_of($anyOf, $subject){
	if(!is_array($anyOf))$anyOf = [$anyOf];
	$last_c = null;
	foreach($anyOf as $width){
		$c = mb_strlen($width);
		if(!isset($comparable) || $c !== $last_c){
			$last_c = $c;
			$comparable = mb_substr($subject,0,-$c);
		}
		if($comparable === $width){
			return true;
		}
	}
	return false;
}

/**
 * Вернет количество повторений $needle строки, начиная от конца $subject
 * @param $needle
 * @param $subject
 * @return int
 */
function str_get_repeat_end($needle, $subject){
	if(!$subject)return 0;
	$c = mb_strlen($needle);
	$repeating_count = 0;
	while($subject){
		$token = substr($subject,-$c);
		$subject = substr($subject,0,-$c);
		if($needle === $token){
			$repeating_count++;
		}else{
			break;
		}
	}
	return $repeating_count;
}
/**
 * Вернет количество повторений $needle строки, начиная от начала $subject
 * @param string $needle
 * @param string $subject
 * @return int
 */
function str_get_repeat_start($needle, $subject){
	if(!$subject)return 0;
	$c = mb_strlen($needle);
	$repeating_count = 0;
	while($subject){
		$token = substr($subject,0,$c);
		$subject = substr($subject,$c);
		if($needle === $token){
			$repeating_count++;
		}else{
			break;
		}
	}
	return $repeating_count;
}





/**
 * вытаскивает один символ с начала строки
 * @param string $string
 * @return string
 */
function string_shift( & $string){
	$char = mb_substr($string,0,1);
	$string = mb_substr($string,1);
	return $char;
}

/**
 * вытаскивает один символ с конца строки
 * @param string $string
 * @return string
 */
function string_pop( & $string){
	$char = mb_substr($string,-1);
	$string = mb_substr($string,0,-1);
	return $char;
}



$pattern_chunk_1 = '\\((\d+)\.(\w+)\\)';        // two captured groups, 1 and 2
$pattern_chunk_2 = '\\\\((\d+)\.(\w+)\\\\)';    // three captured groups, 1 and 2 and 3
$pattern_chunk_3 = '((\d+)\.(\w+)) (?>\.\w)';   // three captured groups and has not capturing groups (total captured = 3)
$pattern_chunk_4 = '\\((\d+)\.(\w+)\\) (';      // ERROR analyze_groups

echo count_masks($pattern_chunk_1) . ' ' . $pattern_chunk_1 . "<br/>";
echo count_masks($pattern_chunk_2) . ' ' . $pattern_chunk_2 . "<br/>";
echo count_masks($pattern_chunk_3) . ' ' . $pattern_chunk_3 . "<br/>";
echo count_masks($pattern_chunk_4) . ' ' . $pattern_chunk_4;



echo '<pre>';
print_r(analyze_groups($pattern_chunk_1));
print_r(analyze_groups($pattern_chunk_2));
print_r(analyze_groups($pattern_chunk_3));
print_r(analyze_groups($pattern_chunk_4));
echo '</pre>';
