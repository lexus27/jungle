<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.10.2016
 * Time: 14:03
 */
namespace Jungle\User\Verification {

	/**
	 * Class Verification
	 * @package Jungle\User\Verification
	 */
	interface VerificationInterface{

		/**
		 * @param $scope
		 * @param TokenStorageInterface $storage
		 * @param Verificator $verificator
		 * @return mixed
		 */
		public function verify($scope, TokenStorageInterface $storage, Verificator $verificator);

	}
}

