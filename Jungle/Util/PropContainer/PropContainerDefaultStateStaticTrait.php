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
	 * Class PropContainerDefaultStateStaticTrait
	 * @package Jungle\Util
	 */
	trait PropContainerDefaultStateStaticTrait{

		/** @var array  */
		protected static $_exclude_default_properties = [];

		/**
		 * Store current object property values to $default_properties
		 */
		protected function _initializeDefaultPropertiesState(){
			if(!isset(static::$_exclude_default_properties['default_properties'])){
				$configuration = [
					'excluded_properties' => static::$_exclude_default_properties,
					'default_properties' => array_diff_key(get_object_vars($this), array_flip(static::$_exclude_default_properties))
				];
				static::$_exclude_default_properties = $configuration;
			}
		}


		/**
		 * Reset each $default_properties to this->{$property_name}
		 */
		protected function _restoreDefaultPropertiesState(){
			foreach(static::$_exclude_default_properties['default_properties'] as $k => $v){
				$this->{$k} = $v;
			}
		}


	}
}
