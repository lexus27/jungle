<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 13:52
 */

namespace Jungle\Util\Smart\Value {

	/**
	 * Phalcon dependency injected , temporal
	 */
	use Jungle\RegExp;

	/**
	 * Class String
	 * @package Jungle\Util\Smart\Value
	 */
	class String extends Value{

		/**
		 * @var string
		 */
		protected static $default_camelize_delimiter = '_';

		/**
		 * @var array
		 */
		protected static $camelize_word_delimiters = ['-','_'];

		/**
		 * @var string
		 */
		protected static $default_value = '';

		/**
		 * @return $this
		 */
		public function camelize(){
			$val = $this->getRaw();
			return $this->setValue(\Jungle\Util\Value\String::camelize($val));
		}

		/**
		 * @return $this
		 */
		public function uncamelize(){
			$val = $this->getRaw();
			return $this->setValue(\Jungle\Util\Value\String::uncamelize($val));
		}

		/**
		 * @param null $separator
		 * @return $this
		 */
		public function increment($separator = null){
			$val = $this->getRaw();
			return $this->setValue(\Jungle\Util\Value\String::increment($val, $separator));
		}

		/**
		 * @return $this
		 */
		public function lower(){
			$val = $this->getRaw();
			return $this->setValue(mb_strtolower($val));
		}

		/**
		 * @return $this
		 */
		public function upper(){
			$val = $this->getRaw();
			return $this->setValue(mb_strtoupper($val));
		}

	}
}