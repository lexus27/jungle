<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 15:46
 */
namespace Jungle\Storage\Db\Query {

	/**
	 * Class Condition
	 * @package Jungle\Storage\Db\Query
	 */
	class Condition{

		/**
		 * @param $condition
		 */
		public static function parseCondition($condition){

			if(is_string($condition)){

				if(preg_split('@\s+(AND|OR)\s+@i',$condition)){

				}

			}elseif(is_array($condition)){



			}


		}

	}
}

