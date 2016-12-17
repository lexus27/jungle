<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 15:07
 */
namespace Jungle\Data\Record\Formula {

	/**
	 * Class EncoderCallback
	 * @package Jungle\Data\Record\Formula
	 */
	class EncoderCallback extends FormulaEncoder{

		protected $callback;

		/**
		 * EncoderCallback constructor.
		 * @param Formula $formula
		 * @param callable $callback
		 */
		public function __construct(Formula $formula, callable $callback){
			$this->formula = $formula;
			$this->callback = $callback;
		}


		/**
		 * @param $fetched
		 * @return mixed
		 */
		public function encodeFetched($fetched){
			return call_user_func($this->callback, $fetched);
		}
	}
}

