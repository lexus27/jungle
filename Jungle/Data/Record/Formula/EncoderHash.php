<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 14:56
 */
namespace Jungle\Data\Record\Formula {

	/**
	 * Class EncoderHash
	 * @package Jungle\Data\Record\Formula
	 */
	class EncoderHash extends FormulaEncoder{

		protected $algo;

		/**
		 * EncoderHash constructor.
		 * @param Formula $formula
		 * @param $algo
		 */
		public function __construct(Formula $formula, $algo){
			parent::__construct($formula);
			$this->formula = $formula;
			$this->algo = $algo;
		}

		/**
		 * @param $fetched
		 * @return string
		 */
		public function encodeFetched($fetched){
			return hash($this->algo, $fetched);
		}

	}
}

