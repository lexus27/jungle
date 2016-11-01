<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.10.2016
 * Time: 11:59
 */
namespace Jungle\User\Verification {

	/**
	 * Class Hint
	 * @package Jungle\User\Verification
	 */
	class Hint extends RequiringException implements HintInterface{

		/** @var  string */
		protected $_verification_key;

		/** @var string  */
		protected $_scope_key;

		/** @var array  */
		protected $_hint = [];

		protected $state;

		/**
		 * Hint constructor.
		 * @param string $state
		 * @param string $scopeKey
		 * @param string $verificationKey
		 * @param array $hint
		 */
		public function __construct($state, $scopeKey, $verificationKey,array $hint){
			$this->_scope_key = $scopeKey;
			$this->_verification_key = $verificationKey;
			$this->_state = $state;
			$this->_hint = $hint;
			parent::__construct('Verification Hinting...');
		}

		/**
		 * @return string
		 */
		public function getScopeKey(){
			return $this->_scope_key;
		}

		/**
		 * @return int
		 */
		public function getVerificationKey(){
			return $this->_verification_key;
		}

		/**
		 * @return bool
		 */
		public function isVerifyIgnored(){
			return $this->_state === Verification::STATE_REQUIRING;
		}

		/**
		 * @return bool
		 */
		public function isVerifyWrong(){
			return $this->_state === Verification::STATE_SATISFY_MISSING;
		}

		/**
		 * @return bool
		 */
		public function isVerifyMissing(){
			return $this->_state === Verification::STATE_SATISFY_WRONG;
		}


		/**
		 * @param $name
		 * @return null
		 */
		public function __get($name){
			return isset($this->_hint[$name])?$this->_hint[$name]:null;
		}

	}
}

