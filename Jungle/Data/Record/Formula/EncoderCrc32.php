<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 15:05
 */
namespace Jungle\Data\Record\Formula {

	/**
	 * Class EncoderCrc32
	 * @package Jungle\Data\Record\Formula
	 */
	class EncoderCrc32 extends FormulaEncoder{

		/** @var bool  */
		protected $abs = true;

		/**
		 * EncoderCrc32 constructor.
		 * @param Formula $formula
		 * @param bool|true $abs
		 */
		public function __construct(Formula $formula, $abs = true){
			$this->formula = $formula;
			$this->abs = $abs;
		}

		/**
		 * @param $fetched
		 * @return mixed
		 */
		public function encodeFetched($fetched){
			return $this->abs?abs(crc32($fetched)):crc32($fetched);
		}
	}
}

