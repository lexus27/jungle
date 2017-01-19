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

		/** @var string */
		protected $pattern;

		/**
		 * Replacer constructor.
		 * @param null $open
		 * @param null $close
		 * @param string $pattern
		 */
		public function __construct($open = null, $close = null, $pattern = null){
			$this->open = $open?:'{{';
			$this->close = $close?:'}}';
			$this->pattern = $pattern?:'\w+';
		}


		/**
		 * @param $text
		 * @param callable $evaluator
		 * @return mixed
		 */
		public function replace($text, callable $evaluator){
			return preg_replace_callback('@'.preg_quote($this->open).'('.$this->pattern.')'.preg_quote($this->close).'@S', function($m) use($evaluator){
				return call_user_func($evaluator, $m[1]);
			}, $text);
		}

	}
}

