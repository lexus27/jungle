<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 13:52
 */

namespace Jungle\Smart\Value {

	use Phalcon\Text as PhalconText;

	/**
	 * Class String
	 * @package Jungle\Smart\Value
	 */
	class String extends Value{

		protected static $default_value = '';

		/**
		 * @return $this
		 */
		public function camelize(){
			return $this->setValue(PhalconText::camelize($this->value));
		}

		/**
		 * @return $this
		 */
		public function uncamelize(){
			return $this->setValue(PhalconText::uncamelize($this->value));
		}

		/**
		 * @param null $separator
		 * @return $this
		 */
		public function increment($separator = null){
			return $this->setValue(PhalconText::increment($this->value, $separator));
		}

		/**
		 * @return $this
		 */
		public function lower(){
			return $this->setValue(PhalconText::lower($this->value));
		}

		/**
		 * @return $this
		 */
		public function upper(){
			return $this->setValue(PhalconText::upper($this->value));
		}

	}
}