<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.07.2016
 * Time: 21:20
 */
namespace Jungle\Lexicon {

	/**
	 * Class Language
	 * @package Jungle\Lexicon
	 */
	class Language{

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $code = 'ru';

		/** @var  string */
		protected $code_extended = 'rus';

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

	}
}

