<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 20:36
 */
namespace Jungle\Util\Replacer {

	/**
	 * Interface ReplacerInterface
	 * @package Jungle\Util\Replacer
	 */
	interface ReplacerInterface{

		/**
		 * @param $text
		 * @param callable $evaluator
		 * @return string
		 */
		public function replace($text, callable $evaluator);

	}



}

