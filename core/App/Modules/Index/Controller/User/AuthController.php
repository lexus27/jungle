<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.07.2016
 * Time: 20:20
 */
namespace App\Modules\Index\Controller\User {
	
	use App\Model\User;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\Application\Notification\Responsible;
	use Jungle\Di\Injectable;

	/**
	 * Class Auth
	 * @package App\Modules\Index\Controller\User
	 */
	class AuthController extends Injectable{

		/**
		 * @param Process $process
		 * @return bool|\Jungle\Data\Record\Model
		 * @throws Responsible
		 */
		public function loginAction(Process $process){
			if(!$this->account->getUser()){
				if($process->login){
					$login = $process->login;
					$password = $process->password;
					/** @var User $user */
					$user = User::findFirst(['username' => $login]);
					if($user){
						$auth = \Jungle\User\AccessAuth\Auth::getAccessAuth([$login, $password]);
						if($auth->match($user->getProperty('password'))){
							$this->account->setUser($user);
							return [
								'user' => $user
							];
						}else{
							throw new Responsible('User verify error',1);
						}
					}else{
						throw new Responsible('User verify error 2',1);
					}
				}
			}
			return null;
		}

		/**
		 *
		 */
		public function logoutAction(){
			if($this->account->getUser()){
				$this->account->setUser(null);
			}
			return true;
		}

	}
}

