<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 1:26
 */
namespace Jungle\User\OldSession\Adapter {
	
	use Jungle\User\OldSession\Adapter;
	use Jungle\User\SessionInterface;

	/**
	 * Class Files
	 * @package Jungle\User\OldSession\Adapter
	 */
	class Files extends Adapter{

		protected $dirname;

		/**
		 * @param $id
		 * @return SessionInterface[]
		 */
		protected function _getUserSessionsById($id){
			// TODO: Implement _getUserSessionsById() method.
		}

		/**
		 * @param $id
		 * @return void
		 */
		protected function _closeUserSessionsById($id){
			// TODO: Implement _closeUserSessionsById() method.
		}

		/**
		 * @param $ip
		 * @return SessionInterface[]
		 */
		public function getIpSessions($ip){
			// TODO: Implement getIpSessions() method.
		}

		/**
		 * @param $ip
		 * @return $this
		 */
		public function closeIpSessions($ip){
			// TODO: Implement closeIpSessions() method.
		}

		/**
		 * @param $id
		 * @return $this
		 */
		public function removeSession($id){
			// TODO: Implement removeSession() method.
		}

		/**
		 * @param $id
		 * @return SessionInterface|null
		 */
		public function getSession($id){
			// TODO: Implement getSession() method.
		}

		/**
		 * @return mixed
		 */
		public function refresh(){
			// TODO: Implement gc() method.
		}

		/**
		 * @return mixed
		 */
		public function factorySession(){
			// TODO: Implement factorySession() method.
		}
	}
}

