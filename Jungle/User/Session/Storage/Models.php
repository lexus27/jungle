<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 20:52
 */
namespace Jungle\User\Session\Storage {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Model;
	use Jungle\Data\Storage\Db\ConditionTarget\Clean;
	use Jungle\User\Session\Storage;
	use Jungle\User\SessionInterface;
	
	/**
	 * Class Models
	 * @package Jungle\User\Session\Storage
	 */
	class Models extends Storage{
		
		protected $session_class_name;
		
		/**
		 * Models constructor.
		 * @param $session_classname
		 */
		public function __construct($session_classname){
			$this->session_class_name = $session_classname;
		}
		
		/**
		 * @param $ip
		 * @return SessionInterface[]
		 */
		public function getIpSessions($ip){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			return $sessionClassName::find([
				['registered_ip','=',$ip]
			]);
		}
		
		/**
		 * @param $ip
		 * @return $this
		 */
		public function closeIpSessions($ip){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			$sessionClassName::deleteCollection([
				['registered_ip','=',$ip],
				['permanent','!=',true]
			]);
			return $this;
		}
		
		/**
		 * @param $id
		 * @return $this
		 */
		public function removeSession($id){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			$sessionClassName::deleteCollection([
				['id','=',$id]
			]);
			return $this;
		}
		
		/**
		 * @param $id
		 * @return SessionInterface|null
		 */
		public function getSession($id){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			return $sessionClassName::findFirst([
				['id','=',$id]
			]);
		}
		
		/**
		 * @param $lifetime
		 * @return void
		 */
		public function clean($lifetime){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			if($lifetime === null){
				$lifetime = 86000 * 2;
			}
			$sessionClassName::deleteCollection([
				new Clean('TIMESTAMP(`modify_time`) >= '.(time() - $lifetime)),
				['permanent','!=',true]
			]);
		}
		
		/**
		 * @return mixed
		 */
		public function factorySession(){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			return new $sessionClassName();
		}
		
		/**
		 * @param $id
		 * @return SessionInterface[]
		 */
		protected function _getUserSessionsById($id){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			return $sessionClassName::find([
				['user_id','=',$id]
			]);
		}
		
		/**
		 * @param $id
		 * @return void
		 */
		protected function _closeUserSessionsById($id){
			/** @var Model $sessionClassName */
			$sessionClassName = $this->session_class_name;
			$sessionClassName::deleteCollection([
				['user_id', '=', $id],
				['permanent','!=',true]
			]);
		}
		
		/**
		 * @param Record|SessionInterface $session
		 * @return $this
		 */
		public function save(SessionInterface $session){
			$session->setModifyTime(time());
			$session->save();
			return $this;
		}

	}
}

