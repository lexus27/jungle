<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 20:40
 */
namespace Jungle\User {
	
	use Jungle\Application\Dispatcher;
	use Jungle\Di\DiInterface;
	use Jungle\Di\Injectable;
	use Jungle\User\Session\Exception;
	use Jungle\User\Session\ProviderInterface;
	use Jungle\User\Session\StorageInterface;
	
	/**
	 * Class SessionManager
	 * @package Jungle\User
	 */
	class SessionManager extends Injectable implements SessionManagerInterface, SessionInterface{

		/** @var  StorageInterface */
		protected $storage;


		/** @var  ProviderInterface|null */
		protected $current_provider;

		/** @var  ProviderInterface|null */
		protected $default_provider;

		/** @var  ProviderInterface[]  */
		protected $providers = [];


		/** @var  bool */
		protected $known = true;

		/** @var  SessionInterface|null */
		protected $current_session;



		/** @var  int */
		protected $lifetime;

		/** @var  int */
		protected $signature_lifetime;


		/**
		 * @param $lifetime
		 * @return $this
		 */
		public function setLifetime($lifetime){
			$this->lifetime = $lifetime;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getLifetime(){
			return $this->lifetime;
		}

		/**
		 * @param $signature_lifetime
		 * @return $this
		 */
		public function setSignatureLifetime($signature_lifetime){
			$this->signature_lifetime = $signature_lifetime;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getSignatureLifetime(){
			return $this->signature_lifetime;
		}

		/**
		 * @return $this
		 */
		protected function _matchStrategy(){
			foreach($this->providers as $strategy){
				if($strategy->hasSignal()){
					return $strategy;
				}
			}
			return $this->default_provider;
		}

		/**
		 * @return ProviderInterface|null
		 */
		public function getCurrentProvider(){
			if(!$this->current_provider){
				return $this->current_provider = $this->_matchStrategy();
			}
			return $this->current_provider;
		}

		/**
		 * @param ProviderInterface $strategy
		 * @return $this
		 */
		public function setDefaultProvider(ProviderInterface $strategy){
			$this->default_provider = $strategy;
			return $this;
		}

		/**
		 * @return ProviderInterface|null
		 */
		public function getDefaultProvider(){
			return $this->default_provider;
		}

		/**
		 * @param array $strategies
		 * @param bool $merge
		 * @return $this
		 */
		public function setProviders(array $strategies, $merge = false){
			$this->providers = $merge?array_replace($this->providers, $strategies):$strategies;
			return $this;
		}

		/**
		 * @param StorageInterface $storage
		 * @return $this
		 */
		public function setStorage(StorageInterface $storage){
			$this->storage = $storage;
			return $this;
		}


		/**
		 * @return StorageInterface
		 */
		public function getStorage(){
			return $this->storage;
		}

		/**
		 * @param $name
		 * @param $value
		 */
		public function set($name, $value){
			$this->requireSession()->set($name,$value);
		}

		/**
		 * @param $name
		 */
		public function remove($name){
			$this->requireSession()->remove($name);
		}


		/**
		 * @param $name
		 * @param $value
		 */
		public function __set($name, $value){
			$this->requireSession()->set($name,$value);
		}

		/**
		 * @param $name
		 */
		public function __unset($name){
			$this->requireSession()->remove($name);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function get($name){
			if($this->known){
				if($this->current_session){
					return $this->current_session->get($name);
				}else{
					try{
						$session = $this->getCurrentProvider()->requireSession($this->storage, true);
						if($session){
							$this->current_session = $session;
							$this->known = true;
							return $session->get($name);
						}else{
							$this->known = false;
							return null;
						}
					}catch(Exception $e){
						$this->known = false;
						return null;
					}
				}
			}else{
				return null;
			}
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function has($name){
			if($this->known){
				if($this->current_session){
					return $this->current_session->get($name);
				}else{
					try{
						$session = $this->getCurrentProvider()->requireSession($this->storage, true);
						if($session){
							$this->current_session = $session;
							$this->known = true;
							return $session->has($name);
						}else{
							$this->known = false;
							return false;
						}
					}catch(Exception $e){
						$this->current_session = $e->getSession();
						$this->known = false;
						return false;
					}
				}
			}else{
				return false;
			}
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function __get($name){
			if($this->known){
				if($this->current_session){
					return $this->current_session->get($name);
				}else{
					try{
						$session = $this->getCurrentProvider()->requireSession($this->storage, true);
						if($session){
							$this->current_session = $session;
							$this->known = true;
							return $session->get($name);
						}else{
							$this->known = false;
							return null;
						}
					}catch(Exception $e){
						$this->known = false;
						return null;
					}
				}
			}else{
				return null;
			}
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			if($this->known){
				if($this->current_session){
					return $this->current_session->get($name);
				}else{
					try{
						$session = $this->getCurrentProvider()->requireSession($this->storage, true);
						if($session){
							$this->current_session = $session;
							$this->known = true;
							return $session->has($name);
						}else{
							$this->known = false;
							return false;
						}
					}catch(Exception $e){
						$this->current_session = $e->getSession();
						$this->known = false;
						return false;
					}
				}
			}else{
				return false;
			}
		}

		/**
		 * @return SessionInterface|null
		 */
		public function requireSession(){
			if(!$this->current_session){
				$session = $this->getCurrentProvider()->requireSession($this->storage);
				$this->current_session = $session;
				$this->known = true;
			}
			return $this->current_session;
		}

		/**
		 * @return bool|SessionInterface
		 */
		public function readSession(){
			if($this->known){
				if($this->current_session){
					return $this->current_session;
				}else{
					try{
						$session = $this->getCurrentProvider()->requireSession($this->storage, true);
						if($session){
							$this->current_session = $session;
							$this->known = true;
							return $session;
						}else{
							$this->known = false;
							return null;
						}
					}catch(Exception $e){
						$this->current_session = $e->getSession();
						$this->known = false;
						return null;
					}
				}
			}else{
				return null;
			}
		}

		public function setDi(DiInterface $di){
			if($di !== $this->_dependency_injection){
				$e = $di->getRoot()->getShared('event_manager');
				$e->attachListener('dispatcher:afterDispatch',function($is_dispatch_error, Dispatcher $dispatcher){
					try{
						if($this->current_session){
							$this->storage->save($this->current_session);
						}
					}catch(\Exception $e){
						$dispatcher->handleException($e,false);
					}
				});
			}
			parent::setDi($di);
		}


		/**
		 *
		 */
		public function __destruct(){
			/*
			 * @FIXME
			 * Сохранение сессии в детрукторе, нецелесообразно по следующим причинам:
			 *      Во время деструкции менеджера, диспетчера уже нет
			 *
			 * Лучше сохранять сессию, при каждом завершении работы диспетчеризации
			 * Это полезно в том числе при разделении работы нескольких приложений в 1 рунтайме.
			 *
			 *
			 */
//			try{
//				if($this->current_session){
//					$this->storage->save($this->current_session);
//				}
//			}catch(\Exception $e){
//				$this->getDi()->getShared('dispatcher')->handleException($e,false);
//			}
		}
		
		/**
		 * @param UserInterface $user
		 * @return mixed
		 */
		public function setUser(UserInterface $user = null){
			$this->requireSession()->setUser($user);
			return $this;
		}
		
		/**
		 * @return UserInterface|null
		 */
		public function getUser(){
			$sess = $this->readSession();
			if($sess) return $sess->getUser();
			return null;
		}
		
		/**
		 * @return mixed
		 */
		public function getUserId(){
			$sess = $this->readSession();
			if($sess) return $sess->getUserId();
			return null;
		}
		
		/**
		 * @return bool
		 */
		public function hasUser(){
			$sess = $this->readSession();
			if($sess) return $sess->hasUser();
			return false;
		}
		
		/**
		 * @param $id
		 * @return mixed
		 */
		public function setSessionId($id){
			$this->requireSession()->setSessionId($id);
			return $this;
		}
		
		/**
		 * @return mixed
		 */
		public function getSessionId(){
			$sess = $this->readSession();
			if($sess) return $sess->getSessionId();
			return null;
		}
		
		/**
		 * @param bool|true $token
		 * @return mixed
		 */
		public function setToken($token = true){
			$this->requireSession()->setToken($token);
			return $this;
		}
		
		/**
		 * @return bool
		 */
		public function isToken(){
			$sess = $this->readSession();
			if($sess) return $sess->isToken();
			return null;
		}
		
		/**
		 * @param bool|true $permanent
		 * @return mixed
		 */
		public function setPermanent($permanent = true){
			$this->requireSession()->setPermanent($permanent);
			return $this;
		}
		
		/**
		 * @return bool
		 */
		public function isPermanent(){
			$sess = $this->readSession();
			if($sess) return $sess->isPermanent();
			return null;
		}
		
		/**
		 * @param $permissions
		 * @return mixed
		 */
		public function setPermissions($permissions){
			$this->requireSession()->setPermissions($permissions);
			return $this;
		}
		
		/**
		 * @return mixed
		 */
		public function getPermissions(){
			$sess = $this->readSession();
			if($sess) return $sess->getPermissions();
			return null;
		}
		
		/**
		 * @param $ip
		 * @return $this
		 */
		public function setRegisteredIp($ip){
			$this->requireSession()->setRegisteredIp($ip);
			return $this;
		}
		
		/**
		 * @return string
		 */
		public function getRegisteredIp(){
			$sess = $this->readSession();
			if($sess) return $sess->getRegisteredIp();
			return null;
		}
		
		/**
		 * @param $user_agent
		 * @return $this
		 */
		public function setRegisteredUserAgent($user_agent){
			$this->requireSession()->setRegisteredUserAgent($user_agent);
			return $this;
		}
		
		/**
		 * @return string
		 */
		public function getRegisteredUserAgent(){
			$sess = $this->readSession();
			if($sess) return $sess->getRegisteredUserAgent();
			return null;
		}
		
		/**
		 * @param array $data
		 * @return mixed
		 */
		public function setData(array $data){
			$this->requireSession()->setData($data);
			return $this;
		}
		
		/**
		 * @return mixed
		 */
		public function getData(){
			$sess = $this->readSession();
			if($sess) return $sess->getData();
			return null;
		}
		
		/**
		 * @param $time
		 * @return mixed
		 */
		public function setModifyTime($time){
			$this->requireSession()->setModifyTime($time);
		}
		
		/**
		 * @return int
		 */
		public function getModifyTime(){
			$sess = $this->readSession();
			if($sess) return $sess->getModifyTime();
			return null;
		}
		
		/**
		 * @param $time
		 * @return mixed
		 */
		public function setCreateTime($time){
			$this->requireSession()->setCreateTime($time);
		}
		
		/**
		 * @return int
		 */
		public function getCreateTime(){
			$sess = $this->readSession();
			if($sess) return $sess->getCreateTime();
			return null;
		}
	}
}

