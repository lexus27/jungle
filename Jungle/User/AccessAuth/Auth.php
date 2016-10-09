<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.01.2016
 * Time: 0:32
 */
namespace Jungle\User\AccessAuth {

	/**
	 * Class Auth
	 * @package Jungle\User\Pair\Pair
	 */
	class Auth implements IPair{

		/** @var Pair[] */
		protected static $auth_collection = [];

		protected static $_allow_set_pair_on_construct = true;

		/** @var Pair */
		protected $pair;

		/**
		 * @param $login
		 * @param $password
		 */
		public function __construct($login = null, $password = null){
			if(static::$_allow_set_pair_on_construct){
				$this->pair = self::getPair($login,$password);
			}
		}


		/**
		 * @return array [login,password]
		 */
		protected function _getPair(){
			return $this->pair?[ $this->pair->getLogin(), $this->pair->getPassword()]:[ null, null];
		}

		/**
		 * @param $login
		 * @return $this
		 */
		public function setLogin($login){
			if($this->getLogin() !== $login){
				$this->pair = self::getPair($login, $this->getPassword());
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getLogin(){
			return $this->pair->getLogin();
		}

		/**
		 * @return string
		 */
		public function getBase64Login(){
			return $this->pair->getBase64Login();
		}

		/**
		 * @param $password
		 * @return $this
		 */
		public function setPassword($password){
			if($this->getPassword() !== $password){
				$this->pair = self::getPair($this->getLogin(), $password);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPassword(){
			return $this->pair->getPassword();
		}

		/**
		 * @return string
		 */
		public function getBase64Password(){
			return $this->pair->getBase64Password();
		}

		/**
		 * @param array $options
		 * @return bool|string
		 */
		public function hash(array $options = []){
			return $this->pair->hash($options);
		}

		/**
		 * @param $passwordHash
		 * @return bool
		 */
		public function match($passwordHash){
			return $this->pair->match($passwordHash);
		}


		/**
		 * @param array|string $access_auth
		 *      @array  - [Login,Password]
		 *      @array  - [
		 *                  'login'     => %Login%,
		 *                  'password'  => %Password%
		 *                ]
		 *      @string - {.}Login:Password
		 *      @string - [.]Login:Password
		 *      @string - [{.}Login:Password]
		 *      @string - {[.]Login:Password}
		 *      @string - <[.]Login:Password>
		 *      @string - <{.}Login:Password>
		 * @return array
		 */
		protected static function parsePair($access_auth){
			if(is_array($access_auth)){
				if(isset($access_auth[0]) && $access_auth[0] && isset($access_auth[1])){
					return $access_auth;
				}elseif(isset($access_auth['login']) && $access_auth['login'] && isset($access_auth['password'])){
					return [$access_auth['login'],$access_auth['password']];
				}else{
					throw new \InvalidArgumentException(__METHOD__. ' Error: invalid array definition for recognize as login:password');
				}
			}elseif($access_auth instanceof Pair){
				return [$access_auth->getLogin(),$access_auth->getPassword()];
			}elseif(is_string($access_auth) && $access_auth){
				/**
				 * <[b]BlaBla:BlaBla>
				 * <{b}BlaBla:BlaBla>
				 * [{b}BlaBla:BlaBla]
				 * {[b]BlaBla:BlaBla}
				 * {b}BlaBla:BlaBla
				 * [b]BlaBla:BlaBla
				 */
				if(preg_match('@
					(\<(?:\{(.+)\})?([^:]*):(.*)\>) |
					(\<(?:\[(.+)\])?([^:]*):(.*)\>) |
					(\[(?:\{(.+)\})?([^:]*):(.*)\]) |
					(\{(?:\[(.+)\])?([^:]*):(.*)\}) |
					((?:\{(.+)\})?([^:]*):(.*)) |
					((?:\[(.+)\])?([^:]*):(.*))
				@xsm',$access_auth,$m)){
					$l = null;
					$p = null;
					if(isset($m[1])){
						$l = $m[3];
						$p = $m[4];
						if(strpos($m[2],'b')!==false){
							$p = base64_decode($p);
						}
					}
					if(isset($m[5])){
						$l = $m[7];
						$p = $m[8];
						if(strpos($m[6],'b')!==false){
							$p = base64_decode($p);
						}
					}
					if(isset($m[9])){
						$l = $m[11];
						$p = $m[12];
						if(strpos($m[10],'b')!==false){
							$p = base64_decode($p);
						}
					}
					return [$l,$p];
				}else{
					return [$access_auth,null];
				}
			}else{
				throw new \InvalidArgumentException(__METHOD__. ' Error: invalid definition');
			}
		}

		protected static function searchPair($login,$password){
			foreach(self::$auth_collection as $i => $auth){
				if($auth->getPassword() === $password && $auth->getLogin() === $login){
					return $i;
				}
			}
			return false;
		}

		/**
		 * @param $login
		 * @param $password
		 * @return Pair
		 */
		protected static function getPair($login,$password){
			if(($i = self::searchPair($login,$password))!==false){
				return self::$auth_collection[$i];
			}else{
				self::$auth_collection[] = $auth = new Pair($login,$password);
				return $auth;
			}
		}

		/**
		 * @param $access_auth
		 * @return Auth
		 */
		public static function getAccessAuth($access_auth){
			list($login,$password) = self::parsePair($access_auth);
			self::$_allow_set_pair_on_construct = false;
			$a = new Auth();
			self::$_allow_set_pair_on_construct = true;
			$a->pair = self::getPair($login,$password);
			return $a;
		}

	}
}

