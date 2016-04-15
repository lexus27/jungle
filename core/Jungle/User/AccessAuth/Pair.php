<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.01.2016
 * Time: 2:56
 */
namespace Jungle\User\AccessAuth {

	/**
	 * Class AccessPair
	 * @package Jungle\User
	 *
	 *
	 * Открытая пара Login:Password для предложения к доступу
	 *
	 * Browser {Send Authorization Data (Login:Password)}
	 * -> Pair()
	 * -> (
	 *      Pair->match(Account->getHash()) | Account->match(Pair)
	 *    )
	 *
	 */
	class Pair implements IPair{

		/** @var string - Outside login string */
		protected $login;

		/** @var  string - Outside password string */
		protected $password;

		/**
		 * @param $login
		 * @param $password
		 */
		public function __construct($login = null, $password = null){
			$this->login    = $login;
			$this->password = $password;
		}

		/**
		 * @param $login
		 * @return $this
		 */
		public function setLogin($login){
			$this->login = $login;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getLogin(){
			return $this->login;
		}

		/**
		 * @return string
		 */
		public function getBase64Login(){
			return base64_encode($this->getLogin());
		}

		/**
		 * @param $password
		 * @return $this
		 */
		public function setPassword($password){
			$this->password = $password;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPassword(){
			return $this->password;
		}

		/**
		 * @return string
		 */
		public function getBase64Password(){
			return base64_encode($this->getPassword());
		}

		/**
		 * @param array $options
		 * @return bool|string
		 */
		public function hash(array $options = []){
			return password_hash($this->password,PASSWORD_BCRYPT,$options);
		}

		/**
		 * @param $passwordHash
		 * @return bool
		 */
		public function match($passwordHash){
			return password_verify($this->password, $passwordHash);
		}

		/**
		 * @param $access_auth
		 * @return Pair
		 */
		public static function getAccessAuth($access_auth){
			if(is_array($access_auth)){
				if(isset($access_auth[0]) && $access_auth[0] && isset($access_auth[1])){
					list($login, $password) = $access_auth;
					return new Pair($login,$password);
				}elseif(isset($access_auth['login']) && $access_auth['login'] && isset($access_auth['password'])){
					return new Pair($access_auth['login'],$access_auth['password']);
				}else{
					throw new \InvalidArgumentException(__METHOD__. ' Error: invalid array definition for recognize as login:password');
				}
			}elseif($access_auth instanceof Pair){
				return $access_auth;
			}else{
				throw new \InvalidArgumentException(__METHOD__. ' Error: invalid definition');
			}
		}

	}
}

