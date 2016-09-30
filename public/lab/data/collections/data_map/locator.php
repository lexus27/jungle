<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.04.2016
 * Time: 19:23
 */

namespace DataMap;

$Data = [

	'id'            => 20,
	'name'          => 'Fedor',
	'family'        => 'Semenov',
	'cash'          => [
		'operations'    => [
			[
				'id' => 10,
				'amount' => 5,
				'time' => 213214,
				'info' => [
					'r_i' => 'Seven',
					'rupi' => 'Gurea'
				]
			], [
				'id' => 11,
				'amount' => 2,
				'time' => 213214,
				'info' => [
					'r_i' => 'Erty',
					'rupi' => 'Nuil'
				]
			], [
				'id' => 12,
				'amount' => 3,
				'time' => 213214,
				'info' => [
					'r_i' => 'Fguj',
					'rupi' => 'Poil'
				]
			]
		],
	],
	'children' => [[
		'id'            => 21,
		'name'          => 'Fedor2',
		'family'        => 'Semenov2',
		'cash'          => [
			'operations'    => [
				[
					'id' => 10,
					'amount' => 5,
					'time' => 213214,
					'info' => [
						'r_i' => 'Seven',
						'rupi' => 'Gurea'
					]
				], [
					'id' => 11,
					'amount' => 2,
					'time' => 213214,
					'info' => [
						'r_i' => 'Erty',
						'rupi' => 'Nuil'
					]
				], [
					'id' => 12,
					'amount' => 3,
					'time' => 213214,
					'info' => [
						'r_i' => 'Fguj',
						'rupi' => 'Poil'
					]
				]
			],
		],
		'children' => []
	]]

];

/**
 *
 * Example:
 *      person_id
 *          - Простой доступ к полю
 *      cash:type (DataMap)
 *          - Тип значения в cash
 *      cash:fields
 *          - Список полей в cash
 *      cash:fields:count
 *          - Количество полей в cash
 *      cash.operations
 *          - Вернет коллекцию операций
 *      cash.operations:count
 *          - Количество элементов в вложеной коллекции
 *      cash.operations:first.info.r_i
 *          -
 *      cash.operations:[id = 10].amount
 *          - Доступ к одному элементу вложеной коллекции
 *
 *      cash.operations:[id > 11]:count
 *          - доступ к коллекции по критерии, выберается коллекция далее расчитывается
 *          количество
 *
 *      cash.operations[].command()
 *          - Применить к каждому элементу коллекции метод command()
 */





function handler($query, $data){
	if(!is_array($query))$query = explode('.',$query);

	if(!$query){
		return $data;
	}elseif(is_scalar($data)){
		if($query){
			return null;
		}else{
			return $data;
		}
	}else{
		$key = array_shift($query);
		$modifiers = explode(':',$key);
		$key = array_shift($modifiers);
		$value = null;
		if(is_array($data)){
			if(isset($data[$key])){
				$value = $data[$key];
			}else{
				//error
				$value = null;
			}
		}else if(is_object($data)){
			if(isset($data->{$key})){
				$value = $data->{$key};
			}else{
				//error
			}
		}
		if($modifiers && $value){
			$value = handleModifiers($modifiers, $value);
		}
		return handler($query, $value);
	}
}



function handleModifiers($modifiers, $data){
	if(!is_array($modifiers)) $modifiers = explode(':',$modifiers);
	if(!$modifiers){
		return $data;
	}else{
		$modifier = array_shift($modifiers);
		$data = handleModifier($modifier, $data);
		return handleModifiers($modifiers,$data);
	}
}

/**
 * @param $modifier
 * @param $value
 * @return array|int|null|string
 */
function handleModifier($modifier, $value){

	if(is_numeric($modifier)){
		$modifier = intval($modifier);
		if(is_array($value)){
			$s = array_slice($value,$modifier,1);
			return $s[0];
		}else{
			return null;
		}
	}

	switch($modifier){
		case 'type':
			$value = gettype($value);
			break;
		case 'count':
			if($value instanceof \Countable || is_array($value) || is_object($value)){
				$value = count($value);
			}else{
				//error
				$value = null;
				break(2);
			}
			break;
		case 'first':
			if(is_array($value)){
				$value = array_slice($value,0,1);
				$value = $value[0];
			}else{
				//error
				$value = null;
				break(2);
			}
			break;
		case 'last':
			if(is_array($value)){
				$value = array_slice($value,-1);
				$value = $value[0];
			}else{
				//error
				$value = null;
				break(2);
			}
			break;
		case 'length':
			if(is_string($value)){
				$value = strlen($value);
			}else{
				//error
				$value = null;
				break(2);
			}
			break;
	}
	return $value;
}
echo '<pre>';
var_dump( handler('cash.operations:-1.id',$Data));
var_dump( handler('cash.operations:last.id',$Data));
echo '<br/>';
var_dump( handler('children:0.id',$Data));
var_dump( handler('children:first.id',$Data));



class Locator{

	protected $data;

	protected $delimiter = '.';

	protected $modifier = ':';

	/**
	 * @param $data
	 * @return $this
	 */
	public function setData($data){
		$this->data = $data;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData(){
		return $this->data;
	}

	/**
	 * @param $query_path
	 * @param null $data
	 */
	public function get($query_path, $data = null){

	}

	public function set($query_path, $value){}


	/**
	 * @param $query_path
	 * @param $data
	 */
	public static function & locate($query_path, & $data){
		$null = null;
		if(!is_array($query_path))$query_path = explode('.',$query_path);

		if(!$query_path){
			return $data;
		}elseif(is_scalar($data)){
			if($query_path){
				return $null;
			}else{
				return $data;
			}
		}else{
			$key = array_shift($query_path);
			$modifiers = explode(':',$key);
			$key = array_shift($modifiers);
			$value = null;
			if(is_array($data)){
				if(isset($data[$key])){
					$value = & $data[$key];
				}else{
					//error
					$value = & $null;
				}
			}else if(is_object($data)){
				if(isset($data->{$key})){
					$value = $data->{$key};
				}else{
					//error
				}
			}
			if($modifiers && $value){
				$value = & handleModifiers($modifiers, $value);
			}
			return handler($query_path, $value);
		}
	}
}