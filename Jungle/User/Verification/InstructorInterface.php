<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.10.2016
 * Time: 20:06
 */
namespace Jungle\User\Verification {

	/**
	 * Interface InstructorInterface
	 * @package Jungle\User\Verification
	 */
	interface InstructorInterface{

		/**
		 * @param $verificationKey
		 * @param $instruction
		 * @return $this
		 */
		public function setHint($verificationKey, HintInterface $instruction);

		/**
		 * @param $verificationKey
		 * @return HintInterface
		 */
		public function getHint($verificationKey);

		/**
		 * @return HintInterface[]
		 */
		public function getHints();

	}

}

