<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 0:46
 */
namespace App\Modules\Index\Controller {
	
	use App\Model\User;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\User\AccessAuth\Auth;

	/**
	 * Class UserController
	 * @package App\Modules\Main\Controller
	 */
	class UserController{

		public function indexMetadata(){
			return [
				'dynamic'  => true,
				'requires' => [
					'user' => User::class
				],
			    'rules' => [
				    'user' => [
					    'is' => null,
				        'forward' => '&not_found'
				    ]
			    ],
			    'renderer_rules' => [

			    ],
			    'result' => [
				    'default' => 'user',
			    ]
			];
		}

		/**
		 * @param Process $process
		 * @return User
		 */
		public function indexAction(Process $process){
			return $process->user;
		}

		/**
		 * @param Process $process
		 * @return array|null
		 */
		public function createAction(Process $process){
			if($process->username){
				$user = new User();
				$user->username = $process->username;
				if($process->password !== $process->password_confirm){
					throw new \LogicException('Password is invalid confirmed!');
				}
				$auth = Auth::getAccessAuth([$process->username,$process->password]);
				$user->password = $auth->hash();
				$user->save();
				return [
					'user' => $user
				];
			}
			return null;
		}

		/**
		 * @param Process $process
		 * @return mixed
		 */
		public function updateAction(Process $process){
			/** @var User\Note $note */
			$params = $process->getParams();
			if(isset($params['username'])){
				$user = $process->user;
				return $user->assign($process->getParams(),['username'])->save();
			}
			return $process->user;
		}

		/**
		 * @param Process $process
		 * @return mixed
		 */
		public function deleteAction(Process $process){
			return $process->user->remove();
		}

		/**
		 * @param Process $process
		 * @return User
		 */
		public function notesAction(Process $process){
			return $process->user;
		}

		/**
		 * @param Process $process
		 * @return User\Note
		 */
		public function noteAction(Process $process){
			return $process->note;
		}


	}
}

