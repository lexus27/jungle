<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 1:22
 */
namespace Jungle\User\OldSession {

	use Jungle\User\OldSession;
	use Jungle\User\SessionInterface;
	use Jungle\User\SessionManagerInterface;
	use Jungle\User\UserInterface;
	use Jungle\Util\Value\Time;

	/**
	 * Class Adapter
	 * @package Jungle\User\OldSession
	 */
	abstract class Adapter implements SessionManagerInterface{

		/** @var  int|null */
		protected $lifetime;

		/** @var  SignatureProviderInterface */
		protected $signature_provider;

		/** @var  SessionInterface */
		protected $current;

		protected $strategy;

		/**
		 * @param SignatureProviderInterface $provider
		 * @return mixed
		 */
		public function setSignatureProvider(SignatureProviderInterface $provider){
			$this->signature_provider = $provider;
			$provider->setSessionManager($this);
			return $this;
		}

		/**
		 * @return SignatureProviderInterface
		 */
		public function getSignatureProvider(){
			return $this->signature_provider;
		}

		/**
		 * @param $lifetime
		 * @return $this
		 */
		public function setLifetime($lifetime){
			$this->lifetime = $lifetime;
			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getLifetime(){
			return $this->lifetime;
		}

		/**
		 * @return bool
		 */
		public function isSupplied(){
			if(!$this->current){
				if(!($signature = $this->signature_provider->getSignature())){
					return false;
				}
				if(!($session = $this->getSession($signature))){
					return false;
				}
				$this->current = $session;
			}
			return true;
		}

		/**
		 * @param SessionInterface $session
		 * @return $this
		 */
		public function setCurrent(SessionInterface $session){
			$this->current = $session;
			return $this;
		}

		/**
		 * @return SessionInterface|null
		 */
		public function requireSession(){
			if(!$this->current){

				if(!($signature = $this->signature_provider->getSignature())){

					$this->onNotSupplied();

					$signature = $this->signature_provider->generateSignature();
					$session = $this->factorySession();
					$session->setCreateTime(time());
					$session->setSessionId($this->storeSignature($signature));
					$this->current = $session;
					$this->signature_provider->setSignature($signature);

					return $session;
				}

				if(!($session = $this->getSession($signature))){
					$this->onNotFound();
					$signature = $this->signature_provider->generateSignature();
					$session = $this->factorySession();
					$session->setCreateTime(time());
					$session->setSessionId($this->storeSignature($signature));
					$this->current = $session;
					$this->signature_provider->setSignature($signature);

					return $session;
				}

				if(Time::isOverdue($session->getModifyTime(), $this->getLifetime())){
					$this->onOverdue();
				}


				/**
				 * В случае с токеном, значение подписи может находится:
				 *  В Спец-Заголовке
				 *  В GET параметре (Query)
				 *  В POST параметре
				 *  В теле запроса (Body Object: JSON, XML)
				 * Определение нужного источника значения может быть итерируемым пока ключ не будет найден
				 *
				 * В HTTP Сессиях используется Одна точка где находится подпись: Кука с определенным именем
				 */
				if($signature){
					$session = $this->getSession($signature);
					if($session){
						$this->current = $session;
						if(Time::isOverdue($session->getModifyTime(), $this->getLifetime())){
							$this->onOverdue();
						}else{
							return $session;
						}
					}else{

						/**
						 * HTTP Session Cookie
						 * Неверная подпись , обязует сгенерировать сессию и отослать её подпись в ответ.
						 */

						/**
						 * Token
						 * Неверная подпись , обязует выдать ошибку.
						 */

						/**
						 * Переданная подпись оказалась неверной
						 * В случае с Куками генерируется новая сессия
						 *
						 * Для токенов, требуется выдать ошибку верификации.
						 * Если сессия не передана, то искать нужно токен.
						 * Если передана сессия то и ассоциирование пользователя будет с ней. проверка токена пропускается
						 *
						 */
						$this->onNotFound();
					}
				}else{

					/**
					 * HTTP Session Cookie
					 * Подпись никогда не придет, пока её не переслать в ответ на запрос.
					 */

					/**
					 * Token
					 * Если не приходит подпись для токена
					 */

					/**
					 *
					 * Если подпись не передана, авторизоваться как аноним
					 * Фантомная сессия, запрещает отдавать подпись.
					 * Она нужна для текущего запроса и данные связанные с ней удаляются после запроса
					 *
					 */
					$this->onNotSupplied();
				}
			}
			return $this->current;
		}

		/**
		 * @param $id
		 * @return SessionInterface|null|void
		 */
		public function getSession($id){
			return $this->_getSession($this->storeSignature($id));
		}

		/**
		 * @param $id
		 * @return mixed
		 */
		public function removeSession($id){
			$this->_removeSession($this->storeSignature($id));
			return $this;
		}

		abstract protected function _removeSession($id);

		abstract protected function _getSession($id);


		/**
		 * @Event-Overdue
		 */
		protected function onOverdue(){
			$this->regenerateSession();
		}

		/**
		 * @Event-NotFound
		 */
		protected function onNotFound(){
			$this->regenerateSession();
		}

		/**
		 * @Event-NotSupplied
		 */
		protected function onNotSupplied(){
			$this->regenerateSession();
		}

		/**
		 * @return SessionInterface
		 */
		public function regenerateSession(){
			$signature = $this->signature_provider->generateSignature();
			if($this->current){
				$this->current->setSessionId($this->storeSignature($signature));
			}else{
				$session = $this->factorySession();
				$session->setCreateTime(time());
				$session->setSessionId($this->storeSignature($signature));
				$this->current = $session;
			}
			$this->signature_provider->setSignature($signature);
			return $this->current;
		}

		/**
		 * @param $signature
		 * @return string
		 */
		public function storeSignature($signature){
			return md5($signature);
		}

		/**
		 * @param SessionInterface $session
		 * @return $this|void
		 */
		public function save(SessionInterface $session){
			$session->setModifyTime(time());
		}

		/**
		 *
		 */
		public function __destruct(){
			if($this->current){
				$this->save($this->current);
			}
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function __get($name){
			/**
			 * Если подписи нет
			 *  Отдать NULL
			 * Если сесси нет
			 *  Отдать NULL
			 * Если сессия истекла
			 *  Отдать NULL
			 */
			return $this->getCurrent()->get($name);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function get($name){
			return $this->getCurrent()->get($name);
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function __set($name, $value){
			/**
			 * Если подписи нет
			 *  Зарегистрировать сессию. Если доступно "Автоматическая регистрация"
			 * Если сессии нет
			 *  Создать, НО клиенту отправить новый идентификатор
			 * Если сессия истекла
			 *  Перегенирировать сессию. Если доступно "Автоматическая регистрация"
			 */
			$this->getCurrent()->set($name, $value);
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function set($name, $value){
			$this->getCurrent()->set($name, $value);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function __isset($name){
			return $this->getCurrent()->has($name);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function has($name){
			return $this->getCurrent()->has($name);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function __unset($name){
			return $this->getCurrent()->remove($name);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function remove($name){
			return $this->getCurrent()->remove($name);
		}

		/**
		 * @param UserInterface|mixed $user
		 * @return SessionInterface[]
		 */
		public function getUserSessions($user){
			if($user instanceof UserInterface){
				return $this->_getUserSessionsById($user->getId());
			}else{
				return $this->_getUserSessionsById($user);
			}
		}


		/**
		 * @param UserInterface|mixed $user
		 * @return $this
		 */
		public function closeUserSessions($user){
			if($user instanceof UserInterface){
				$this->_closeUserSessionsById($user->getId());
			}else{
				$this->_closeUserSessionsById($user);
			}
			return $this;
		}

		/**
		 * @param $id
		 * @return SessionInterface[]
		 */
		abstract protected function _getUserSessionsById($id);

		/**
		 * @param $id
		 * @return void
		 */
		abstract protected function _closeUserSessionsById($id);


	}
}

