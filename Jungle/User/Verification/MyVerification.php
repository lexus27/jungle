<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.10.2016
 * Time: 11:41
 */
namespace Jungle\User\Verification {
	
	use Jungle\Di\DiLocatorInterface;
	use Jungle\Http\Request;
	
	/**
	 * Class MyVerification
	 * @package Jungle\User\Verification
	 */
	class MyVerification extends Verification{

		/** @var int  */
		protected $linking_type = self::LINKING_SESSION;

		/** @var string  */
		protected $verification_key = 'test';

		/**
		 * MyVerification constructor.
		 * @param DiLocatorInterface $di
		 */
		public function __construct(DiLocatorInterface $di){
			$this->di = $di;
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
			$open = uniqid('vc_', true);
			$privacy = md5($open);
			$token->setData([
				'hint' => $open,
			]);
			$token->setCode($privacy);
			return true;
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
		protected function before($scope, Verificator $verificator){}

		/**
		 * @param $scope
		 * @param Verificator $verificator
		 */
		protected function after($scope, Verificator $verificator){}


	}
}

