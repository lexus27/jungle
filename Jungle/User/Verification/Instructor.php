<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.10.2016
 * Time: 11:59
 */
namespace Jungle\User\Verification {

	/**
	 * Class Instructor
	 * @package Jungle\User\Verification
	 */
	class Instructor extends RequiringException implements InstructorInterface{

		/** @var  HintInterface[] */
		protected $hints = [];

		/**
		 * @param $verificationKey
		 * @param $instruction
		 * @return $this
		 */
		public function setHint($verificationKey, HintInterface $instruction){
			$this->hints[$verificationKey] = $instruction;
			return $this;
		}

		/**
		 * @param $verificationKey
		 * @return HintInterface|null
		 */
		public function getHint($verificationKey){
			return isset($this->hints[$verificationKey])?$this->hints[$verificationKey]:null;
		}

		/**
		 * @return HintInterface[]
		 */
		public function getHints(){
			return $this->hints;
		}

	}
}

