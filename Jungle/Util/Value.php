<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 15:25
 */
namespace Jungle\Util {

	/**
	 * Class Value
	 * @package Jungle\Util
	 */
	class Value{

		const SCALAR = 1;

		protected function __construct(){}

		/**
		 * @param $var
		 * @param $type
		 */
		public static function settype(& $var, $type){
			self::isValidVartype($type,true);
			settype($var,$type);
		}

		/**
		 * @param $var
		 * @param $type
		 * @return mixed
		 */
		public static function setVartype($var, $type){
			self::isValidVartype($type,true);
			settype($var,$type);
			return $var;
		}

		/**
		 * @param $vartype
		 * @param bool|true $trigger_error
		 * @return bool
		 */
		public static function isValidVartype($vartype, $trigger_error = false){
			$valid = in_array($vartype,['integer','boolean','double','string','array','object','null'],true);
			if(!$valid && $trigger_error){
				trigger_error('set_type error: "'.$vartype.'" is not a valid type passed for conversion!',E_USER_ERROR);
			}
			return $valid;
		}

		/**
		 * @param $vartype
		 * @return bool
		 */
		public static function isMixedVartype($vartype){
			return $vartype === 'mixed';
		}


		/**
		 * @param $value
		 * @return bool|float|int|null
		 */
		public static function actualStringRepresentType($value){
			if(is_numeric($value)){
				if(strpos($value,'.')){
					return floatval($value);
				}
				return intval($value);
			}elseif(strcasecmp($value,'null') === 0){
				return null;
			}elseif(strcasecmp($value,'true') === 0){
				return true;
			}elseif(strcasecmp($value,'false') === 0){
				return false;
			}else{
				return $value;
			}
		}

	}
}

