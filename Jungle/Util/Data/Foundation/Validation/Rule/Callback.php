<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 11:51
 */
namespace Jungle\Util\Data\Foundation\Validation\Rule {
	
	use Jungle\Util\Data\Foundation\Validation\Rule;

	/**
	 * Class Callback
	 * @package Jungle\Util\Data\Foundation\Validation\Rule
	 */
	class Callback extends Rule{

		/** @var  string */
		protected $type = 'Anonymous';

		/** @var  callable */
		protected $callback;

		/**
		 * Callback constructor.
		 * @param callable $callback
		 */
		public function __construct(callable $callback){
			$this->callback = $callback;
		}

		/**
		 * @param $value
		 * @param array $parameters
		 * @return bool
		 */
		public function check($value,array $parameters = []){
			return call_user_func($this->callback, $parameters);
		}

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _check($value){}
	}
}

