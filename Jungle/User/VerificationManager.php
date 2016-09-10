<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.09.2016
 * Time: 13:23
 */
namespace Jungle\User {
	
	use Jungle\Di\Injectable;

	class VerificationManager extends Injectable{

		public function requireVerification(){
			$verification = new Verification();

			$user = $this->account->getUser();



			$sessionId = $this->session->readSession()->getSessionId();


			$verification->setSessionId($sessionId);


		}


	}
}

