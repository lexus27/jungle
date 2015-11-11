<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 27.10.2015
 * Time: 23:46
 */
namespace Jungle {

	use Jungle\TypeHint\Type;

	/**
	 * Class TypeHint
	 * @package Jungle
	 *
     *  TODO \Jungle\TypeHint::check{fast_type_name}($value, $ОшибкаВозниклаПри)
	 *  TODO \Jungle\TypeHint::check($type:array:string,$value, $ОшибкаВозниклаПри)
	 *  TODO @throws \Jungle\TypeHintException[$ОшибкаВозниклаПри, ]
	 *
	 * TODO Реализовать концепцию строгой/мягенькой типизации
	 */
	class TypeHint{

		private static $method_prefix = 'check';

		/** @var Type[]  */
		private static $types = [];

		/**
		 * @param $method
		 * @param $arguments
		 */
		public static function __callStatic($method,$arguments){
			$len = strlen(self::$method_prefix);
			$startWith = substr($method,0,$len);

			if($startWith === self::$method_prefix){
				$checkType = strtolower(substr($method,$len));
				self::check($checkType,$arguments[0],$arguments[1]);
			}

			if(strncmp($method,self::$method_prefix,strlen(self::$method_prefix))){

			}
		}

		/**
		 * @param $type
		 * @param $value
		 * @param string $errorMessage
		 * @return bool
		 */
		public static function check($type,$value,$errorMessage = ''){
			if($type){
				if(is_array($type)){
					foreach($type as $t){
						foreach(self::$types as $tObject){
							if($tObject->parse($t) && !$tObject->check($value,$t)){
								throw new TypeHintException($errorMessage);
							}
						}
					}
				}elseif(is_string($type)){
					foreach(self::$types as $tObject){
						if($tObject->parse($type) && !$tObject->check($value,$type)){
							throw new TypeHintException($errorMessage);
						}
					}
				}else{

				}
			}
		}


		/**
		 * @param Type $type
		 */
		public static function addType(Type $type){
			if(self::searchType($type)===false){
				self::$types[] = $type;
			}
		}

		/**
		 * @param Type $type
		 * @return mixed
		 */
		public static function searchType(Type $type){
			return array_search($type,self::$types,true);
		}

		/**
		 * @param Type $type
		 */
		public static function removeType(Type $type){
			if(($i = self::searchType($type))!==false){
				array_splice(self::$types,$i,1);
			}
		}

	}
}

