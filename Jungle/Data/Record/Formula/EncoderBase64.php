<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 14:38
 */
namespace Jungle\Data\Record\Formula {

	/**
	 * Class EncoderBase64
	 * @package Jungle\Data\Record\Formula
	 */
	class EncoderBase64 extends FormulaEncoder{

		/**
		 * @param $fetched
		 * @return string
		 */
		public function encodeFetched($fetched){
			return base64_encode($fetched);
		}

	}
}

