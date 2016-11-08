<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 20:34
 */
namespace Jungle\Util\Replacer {

	/**
	 * Class Replacer
	 * @package Jungle\Util\Replacer
	 */
	class Replacer{

		/** @var  string */
		protected $open;

		/** @var  string */
		protected $close;

		/**
		 * Replacer constructor.
		 * @param null $open
		 * @param null $close
		 */
		public function __construct($open = null, $close = null){
			$this->open = $open?:'{{';
			$this->close = $close?:'}}';
		}


		/**
		 * @param $text
		 * @param callable $evaluator
		 * @return mixed
		 */
		public function replace($text, callable $evaluator){
			return preg_replace_callback('@'.preg_quote($this->open).'(\w+)'.preg_quote($this->close).'@S', function($m) use($evaluator){
				return call_user_func($evaluator, $m[1]);
			}, $text);
		}

	}
}

