<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 20:59
 */
namespace App\Services\Session {
	
	use App\Model\Session;
	use Jungle\User\SessionInterface;

	/**
	 * Class MyModels
	 * @package App\Services\Session
	 */
	class MyModels extends \Jungle\User\Session\Storage\Models{

		public function __construct(){}

		/**
		 * @param $ip
		 * @return SessionInterface[]
		 */
		public function getIpSessions($ip){
			return Session::find([
				['registered_ip','=',$ip]
			]);
		}

		/**
		 * @param $ip
		 * @return $this
		 */
		public function closeIpSessions($ip){
			/** @var Session[] $sessions */
			Session::deleteCollection([
				['registered_ip','=',$ip]
			]);
			return $this;
		}

		/**
		 * @param $id
		 * @return $this
		 */
		public function removeSession($id){
			Session::deleteCollection(['id','=',$id]);
			return $this;
		}

		/**
		 * @param $id
		 * @return SessionInterface|null
		 */
		public function getSession($id){
			return Session::findFirst($id);
		}

		/**
		 * @param $lifetime
		 * @return mixed
		 */
		public function clean($lifetime){
			if($lifetime === null){
				$lifetime = 86000 * 2;
			}
			Session::deleteCollection(['modify_time','>=',time() - $lifetime]);
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function factorySession(){
			return new Session();
		}

		/**
		 * @param $id
		 * @return SessionInterface[]
		 */
		protected function _getUserSessionsById($id){
			return Session::find([
				['user_id','=',$id]
			]);
		}

		/**
		 * @param $id
		 * @return void
		 */
		protected function _closeUserSessionsById($id){
			Session::deleteCollection([
				['user_id','=',$id]
			]);
		}
	}
}

