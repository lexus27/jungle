<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.10.2016
 * Time: 1:03
 */
namespace Jungle\Util\PropContainer {

	/**
	 * Class PropContainerDefaultStateTrait
	 * @package Jungle\Util
	 */
	trait PropContainerDefaultStateTrait{


		/** @var array  */
		protected static $_exclude_default_properties = [];

		/** @var  array */
		protected $_default_properties = [];

		/**
		 * Store current object property values to $default_properties
		 */
		protected function _initializeDefaultPropertiesState(){
			$excluded = static::$_exclude_default_properties;
			$excluded[] = 'default_properties';
			$this->_default_properties = array_diff_key(get_object_vars($this), array_flip($excluded));
		}


		/**
		 * Reset each $default_properties to this->{$property_name}
		 */
		protected function _restoreDefaultPropertiesState(){
			foreach($this->_default_properties as $k => $v){
				$this->{$k} = $v;
			}
		}


	}
}
