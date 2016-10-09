<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.10.2016
 * Time: 1:03
 */
namespace Jungle\Util {

	/**
	 * Class InternalPropertyStateTrait
	 * @package Jungle\Util
	 */
	trait InternalPropertyStateTrait{


		/** @var array  */
		protected static $exclude_default_properties = [
			'default_properties'
		];

		/** @var  array */
		protected $default_properties = [];

		/**
		 * Store current object property values to $default_properties
		 */
		protected function _initializeDefaultPropertiesState(){
			$excluded = static::$exclude_default_properties;
			$excluded[] = 'default_properties';
			$this->default_properties = array_diff_key(get_object_vars($this), array_flip($excluded));
		}


		/**
		 * Reset each $default_properties to this->{$property_name}
		 */
		protected function _restoreDefaultPropertiesState(){
			foreach($this->default_properties as $k => $v){
				$this->{$k} = $v;
			}
		}


	}
}
