<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.10.2016
 * Time: 16:54
 */
namespace Jungle\User\Verification {

	/**
	 * Class Verification
	 * @package Jungle\User\Verification
	 *
	 *
	 * Верификация
	 * Может быть капчей
	 *      Отображается до запроса на выполнение.
	 *      Отображается после запроса на выполнения, при ответном требовании верифицировать действие (возм. повторное заполнение форм)
	 *
	 * Может отправляться на любой контакт пользователю в соц сети (тр. поддержка интеграции соц-сетей)
	 *
	 * Может быть по запросу на аддресс сылки в письме
	 * Может быть по коду в письме
	 * Может быть по коду в СМС
	 *
	 * Дополнительная секретность токена обеспечивается использованием токена в паре со связанными идентификаторами, или с солью
	 *
	 * Верификация может быть связана с
	 *  Идентификатором пользователя, (username)
	 *  Идентификатором пользователя по текущей сессии, (ограничение браузера)
	 *  Айди сессии (не крос доменная верификация) (ограничение браузера)
	 *  С контактом на который отправляется код (аналог username, Только более конкретизирован)
	 *
	 *
	 * Как происходит проверка и инициализация верификации? - Контроллер (Приложение)
	 *
	 *      Перед вызовом контроллера, В момент вызова Контроллера (определение конструкции вызова верификации),
	 *
	 *
	 *
	 * Отображение - работа с состоянием Верификации
	 *
	 * Состояния отображения по MCA
	 *
	 * Где происходит работа с состоянием Верификации?
	 *
	 *
	 * В контроле так-же происходит проверка верифицирован ли пользователь. Непосредственно перед действием
	 *
	 *
	 */
	abstract class Verification implements VerificationInterface{

		/**
		 * на текущей верификации для данного scope и user_id/session_id был впервые создан токен с секретными данными
		 */
		const STATE_CREATED                 = 'created';

		/**
		 * В этом состоянии ранее был уже затребован токен,
		 * но на текущий момент была попытка снова пройти действие
		 * будто клиенту не понятны инструкции по верификации
		 */
		const STATE_REQUIRING               = 'requiring';


		/**
		 * Токен Удален
		 */
		const STATE_CLEAN                   = 'clean';

		/**
		 * Созданный токен не был удовлетворен с момента его создания,
		 * при этом истек период актуальности секретных данных
		 */
		const STATE_INVOKE_OVERDUE          = 'invoke_overdue';

		/**
		 * Созданный токен был ранее удовлетворен до сих пор,
		 * но истек период активности эффекта удовлетворенности
		 */
		const STATE_SATISFY_OVERDUE         = 'satisfy_overdue';

		/**
		 * На данном этапе токен прошел верификацию
		 */
		const STATE_SATISFY_SUCCESS         = 'satisfy_success';

		/**
		 * Секретные данные для удовлетворения верификации были предоставленны,
		 * но они не прошли проверки
		 */
		const STATE_SATISFY_WRONG           = 'satisfy_wrong';

		/**
		 * На данном этапе верификации не было предоставленно внятных данных для её прохождения
		 */
		const STATE_SATISFY_MISSING         = 'satisfy_missing';



		const LINKING_USER    = 2;
		const LINKING_SESSION = 4;
		const LINKING_ALL     = self::LINKING_SESSION | self::LINKING_USER;

		/** @var int  */
		protected $linking_type = self::LINKING_ALL;

		/** @var  string */
		protected $verification_key;

		/** @var  bool */
		protected $auto_satisfy = true;


		/** @var  string|null */
		protected $current_state;

		/** @var  int|null */
		protected $current_time;

		/** @var  string */
		protected $current_scope;

		/** @var  TokenStorageInterface */
		protected $current_storage;

		/** @var  Verificator */
		protected $current_verificator;

		/** @var bool  */
		protected $current_modified = false;

		/***
		 * @param $scope
		 * @param TokenStorageInterface $storage
		 * @param Verificator $verificator
		 * @return bool
		 *
		 * Обращение к verify происходит каждый раз перед каким либо действием.
		 * Поэтому состояние запрашиваемого токена может быть разным:
		 *
		 *
		 * > Токен не был создан
		 *      > Создание токена > Создание секретных данных > #Требование верификации
		 *
		 *      В данном состоянии клиент попытался выполнить действие, но появляется ошибка о требовании верификации.
		 *
		 *
		 * > Токен пока не Верифицирован
		 *      > Проверка истечения времени актуальности секретных данных
		 *          > Создание секретных данных > #Требование верификации
		 *          > #Требование верификации
		 *      > Приём данных верификации
		 *          > Данные отсутствуют > !Создание секретных данных > #Требование верификации[Отсутствие]
		 *          > Данные не верные > !Создание секретных данных > #Требование верификации[Промах]
		 *          > Совпадение > Верифицирован[ОК]
		 *
		 *      В данном состоянии ожидаются данные от клиента для прохождения верификации.
		 *
		 *      Если данные не верны, то нужно выбросить требование верификации уже с пометкой Промаха
		 *
		 *      Если данных нет, то снова выбрасывается требование верификации(как при создании токена)
		 *      с теми же секретными данными либо с новыми(перегенерация) с пометкой Отсутствия
		 *
		 * > Токен Верифицирован
		 *      > Проверка просроченности верификации
		 *          > Сброс токена > Создание секретных данных > #Требование верификации
		 *          > Нет требований[ОК]
		 *
		 *      Действия при верифицированном токене, проходят без проблем, пока срок действия не истечет
		 *
		 *
		 *
		 *
		 */
		public function verify($scope, TokenStorageInterface $storage, Verificator $verificator){
			if($this->before($scope, $verificator) !== false){
				try{
					$this->current_scope        = $scope;
					$this->current_storage      = $storage;
					$this->current_verificator  = $verificator;
					$this->current_time         = $time = time();
					$this->currentInit();

					$token = $this->loadToken($scope);

					// состояния токена
					if(!$token){
						// Не создан
						$token = $this->factoryToken($scope);
						$token->setInvokeTime($time);
						$this->current_state = self::STATE_CREATED; // Изменен
						$this->current_modified = true;
					}elseif(!$token->isSatisfied()){
						// Не верифицирован
						$invoke_time = $token->getInvokeTime();
						$invoke_lifetime = $this->getInvokeLifetime();
						if($invoke_lifetime !== null && $time > ($invoke_time + $invoke_lifetime)){
							$this->current_state = self::STATE_INVOKE_OVERDUE;
							$this->current_modified = true;
							$token->setInvokeTime($time);
						}elseif($this->auto_satisfy){
							$data = $this->takeAnswer();
							if($data === null){
								$this->current_state = self::STATE_SATISFY_MISSING; // Пока не ясно. Будет ли изменен
							}elseif($this->solveAnswer($token, $data)){
								$this->current_state = self::STATE_SATISFY_SUCCESS;
								$this->current_modified = true;
								$token->setSatisfied(true, $time);
							}else{
								$this->current_state = self::STATE_SATISFY_WRONG; // Пока не ясно. Будет ли изменен
							}
						}else{
							$this->current_state = self::STATE_REQUIRING;
						}
					}else{
						// Верифицирован
						$satisfied_time = $token->getSatisfyTime();
						$satisfied_lifetime = $this->getSatisfyLifetime();
						if($satisfied_lifetime !== null && $time > ($satisfied_time + $satisfied_lifetime)){
							$this->current_state = self::STATE_SATISFY_OVERDUE;
							$this->current_modified = true;
							$token->setSatisfied(false, $time);
							$token->setInvokeTime($time);
						}
					}
					$to_requiring = false;
					if(!in_array($this->current_state,[self::STATE_SATISFY_SUCCESS,null], true)){
						if($this->isHintingState() && $this->hint($token) === true){
							$this->current_modified = true;
						}
						$to_requiring = true;
					}elseif($this->current_state === self::STATE_SATISFY_SUCCESS){
						$this->success($token, $storage);
					}

					if($this->current_modified){
						if($this->current_state === self::STATE_CREATED){
							$storage->createToken($token);
						}else{
							$storage->saveToken($token);
						}
					}
					if($to_requiring){
						$this->requiring($token);
					}
					$this->after($scope, $verificator);
				}finally{
					$this->currentClean();
					$this->current_state        = null;
					$this->current_scope        = null;
					$this->current_storage      = null;
					$this->current_verificator  = null;
				}
			}
		}



		/**
		 * @param $scope
		 * @param $storage
		 * @param $verificator
		 * @param array $params
		 * @return bool|null
		 * Метод предназначается для ручного запуска удовлетворения верификации, там где это нужно.
		 *
		 */
		public function satisfy($scope, TokenStorageInterface $storage, Verificator $verificator, array $params = null){
			try{
				$this->current_scope        = $scope;
				$this->current_storage      = $storage;
				$this->current_verificator  = $verificator;
				$this->current_time         = $time = time();
				$this->currentInit();

				$token = $this->loadToken($scope);

				// состояния токена
				if($token && !$token->isSatisfied()){
					// Не верифицирован
					$invoke_time = $token->getInvokeTime();
					$invoke_lifetime = $this->getInvokeLifetime();
					if($invoke_lifetime !== null && $time > ($invoke_time + $invoke_lifetime)){
						$this->current_state = self::STATE_INVOKE_OVERDUE;
						$this->current_modified = true;
						$token->setInvokeTime($time);
					}elseif($this->auto_satisfy){
						$data = $this->takeAnswer();
						if($data === null){
							$this->current_state = self::STATE_SATISFY_MISSING; // Пока не ясно. Будет ли изменен
						}elseif($this->solveAnswer($token, $data)){
							$this->current_state = self::STATE_SATISFY_SUCCESS;
							$this->current_modified = true;
							$token->setSatisfied(true, $time);
						}else{
							$this->current_state = self::STATE_SATISFY_WRONG; // Пока не ясно. Будет ли изменен
						}
					}else{
						$this->current_state = self::STATE_REQUIRING;
					}

					$to_requiring = false;
					if($this->current_state === self::STATE_SATISFY_SUCCESS){
						$this->success($token, $storage);
					}elseif($this->current_state !== null){
						if($this->hint($token) === true){
							$this->current_modified = true;
						}
						$to_requiring = true;
					}

					if($this->current_modified){
						$storage->saveToken($token);
					}

					if($to_requiring){
						$this->requiring($token);
					}

					$this->after($scope, $verificator);

					return !in_array($this->current_state,[self::STATE_SATISFY_SUCCESS, null], true);
				}
				return null;
			}finally{
				$this->currentClean();
				$this->current_state        = null;
				$this->current_scope        = null;
				$this->current_storage      = null;
				$this->current_verificator  = null;
			}
		}

		/**
		 * @param $scope
		 * @param TokenStorageInterface $storage
		 * @param Verificator $verificator
		 *
		 * Метод для внешнего сброса верификации, в случаях
		 */
		public function clean($scope, TokenStorageInterface $storage, Verificator $verificator){
			try{
				$this->current_scope        = $scope;
				$this->current_storage      = $storage;
				$this->current_verificator  = $verificator;
				$this->current_time         = $time = time();
				$this->currentInit();

				$token = $this->loadToken($scope);
				if($token){
					$storage->removeToken($token);
				}
			}finally{
				$this->currentClean();
				$this->current_state        = null;
				$this->current_scope        = null;
				$this->current_storage      = null;
				$this->current_verificator  = null;
			}
		}

		/**
		 * @param $scope
		 * @param Verificator $verificator
		 * @return bool
		 */
		protected function before($scope, Verificator $verificator){}

		/**
		 * @param $scope
		 * @param Verificator $verificator
		 */
		protected function after($scope, Verificator $verificator){}

		/**
		 * @param $token
		 * @param $storage
		 */
		protected function success(TokenInterface $token,TokenStorageInterface $storage){}

		/**
		 * @return int
		 */
		protected function getSatisfyLifetime(){
			return 3600;
		}

		/**
		 * @return int
		 */
		protected function getInvokeLifetime(){
			return 3600;
		}

		/**
		 * @param $scope
		 * @return TokenInterface
		 */
		protected function factoryToken($scope){
			$token = $this->current_storage->factoryToken($scope, $this->verification_key);
			if($this->linking_type & self::LINKING_SESSION){
				$token->setSessionId($this->current_verificator->getSessionId());
			}
			if($this->linking_type & self::LINKING_USER){
				$token->setUserId($this->current_verificator->getUserId());
			}
			return $token;
		}

		/**
		 * @param $scope
		 * @return TokenInterface|null
		 */
		protected function loadToken($scope){

			if($this->linking_type & self::LINKING_SESSION){
				$session_id = $this->current_verificator->getSessionId();
			}else{
				$session_id = null;
			}

			if($this->linking_type & self::LINKING_USER){
				$user_id = $this->current_verificator->getUserId();
			}else{
				$user_id = null;
			}

			$token = $this->current_storage->loadToken($user_id, $session_id, $scope,$this->verification_key);
			return $token;
		}

		protected function currentInit(){}

		protected function currentClean(){}





		/**
		 * Требование верификации
		 * @param TokenInterface $token
		 */
		abstract protected function requiring(TokenInterface $token);

		/**
		 * @param TokenInterface $token
		 * @return bool
		 */
		abstract protected function hint(TokenInterface $token);

		/**
		 * @param array $strict_answer
		 * @return array|null
		 */
		abstract protected function takeAnswer(array $strict_answer = null);

		/**
		 * @param TokenInterface $token
		 * @param array $answer
		 * @return bool
		 */
		abstract protected function solveAnswer(TokenInterface $token, array $answer);

		/**
		 * @return bool
		 */
		protected function isHintingState(){
			return in_array($this->current_state, [
				self::STATE_CREATED,
				self::STATE_SATISFY_OVERDUE,
				self::STATE_INVOKE_OVERDUE
			], true);
		}


	}
}

