<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.10.2016
 * Time: 23:01
 */
namespace Jungle\User\Verification {

	use Jungle\Di\DiLocatorInterface;
	use Jungle\Http\Request;
	use Jungle\Messenger;

	/**
	 * Class MyVerification2
	 * @package Jungle\User\Verification
	 */
	class MyVerification2 extends Verification{
		
		/** @var int  */
		protected $linking_type = self::LINKING_SESSION;

		/** @var string  */
		protected $verification_key = 'test';
		/**
		 * @var Messenger
		 */
		private $messenger;

		/**
		 * MyVerification constructor.
		 * @param DiLocatorInterface $di
		 * @param Messenger $messenger
		 */
		public function __construct(DiLocatorInterface $di, Messenger $messenger){
			$this->di = $di;
			$this->messenger = $messenger;
		}

		/**
		 * Требование верификации
		 * @param TokenInterface $token
		 * @throws Hint
		 */
		protected function requiring(TokenInterface $token){
			throw new Hint($this->current_state, $this->current_scope, $this->verification_key, $token->getData());
		}

		/**
		 * @param TokenInterface $token
		 * @return bool
		 */
		protected function hint(TokenInterface $token){
			$code = $this->generateCode([3,6]);
			$hash = md5($code);


			$message = new Messenger\Mail\Message();
			$message->setSubject('Подтвердите код');
			$message->setContent('Код для ввода: '.$code.' , введите его в соответствующую форму на сайте');
			$contact = Messenger\Mail\Contact::getContact('Алексей <lexus27.khv@gmail.com>');

			$combination = new Messenger\Combination();
			$combination->addDestination($contact);
			$combination->setMessage($message);

			$this->messenger->send($combination);
			$token->setCode($hash);
			return true;
		}

		/**
		 * @param int $length
		 * @param null $charlist
		 * @return string
		 */
		public function generateCode($length = 10, $charlist = null) {
			$charlist = $charlist?:'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charlist_length = strlen($charlist);
			$randomString = '';
			if(is_array($length)){
				list($min,$max) = $length;
				$length = rand($min,$max);
			}
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $charlist[rand(0, $charlist_length - 1)];
			}
			return $randomString;
		}

		/**
		 * @param array $strict_answer
		 * @return array|null
		 */
		protected function takeAnswer(array $strict_answer = null){
			/** @var Request $request */
			$request = $this->di->getShared('request');

			$answer = [
				'type' => $request->getParam('v_type'),
				'code' => $request->getParam('v_code'),
				'scope' => $request->getParam('v_scope'),
			];

			if(is_array($strict_answer)){
				$answer = array_replace($answer, $strict_answer);
			}

			if(
				$this->current_scope !== $answer['scope'] ||
				$this->verification_key !== $answer['type']
			){
				return null;
			}

			return $answer;
		}

		/**
		 * @param TokenInterface $token
		 * @param array $answer
		 * @return bool
		 */
		protected function solveAnswer(TokenInterface $token, array $answer){
			return $token->getCode() === md5($answer['code']);
		}

		/**
		 * @param $scope
		 * @param Verificator $verificator
		 * @return bool
		 */
		protected function before($scope, Verificator $verificator){

		}

		/**
		 * @param $scope
		 * @param Verificator $verificator
		 */
		protected function after($scope, Verificator $verificator){

		}
	}
}

