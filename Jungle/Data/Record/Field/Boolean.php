<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 23:19
 */
namespace Jungle\Data\Record\Field {

	/**
	 * Class Boolean
	 * @package Jungle\Data\Record\Field
	 */
	class Boolean extends Field{


		public function stabilize($value){
			if($this->nullable && empty($value)){
				return null;
			}
			if(is_string($value)){
				if(strcasecmp($value,'on') || strcasecmp($value,'true')){
					return true;
				}
				if(strcasecmp($value,'off') || strcasecmp($value,'false')){
					return false;
				}
			}
			return boolval($value);
		}

		public function decode($value){
			return boolval($value);
		}

		public function encode($value){
			return $value?1:0;
		}

		public function validate($value){
			return is_bool($value);
		}


	}
}

