<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.01.2016
 * Time: 20:15
 */
namespace Jungle\Communication\URL\Host {

	use Jungle\Communication\URL\Host;

	/**
	 * Class IP
	 * @package Jungle\Communication\URL\Host
	 */
	class IP{

		const TYPE_IPV4 = 1;

		const TYPE_IPV6 = 2;

		/** @var string  */
		protected static $ip_v4_regex = '/^(25[0-5]|2[0-4][0-9]|1?[0-9][0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})){3}$/';

		/** @var string  */
		protected static $ip_v6_regex = '/^([0-9a-f]|:){1,4}(:([0-9a-f]{0,4})*){1,7}$/i';

		/** @var IP[] */
		protected static $ip_collection = [];


		/**
		 * @var int
		 */
		protected $type;


		/**
		 * @var mixed
		 */
		protected $address;

		


		/**
		 * @param $address
		 * @return string
		 */
		public static function normalizeIPV4Address($address){
			return trim(trim($address),'.');
		}

		/**
		 * @param $address
		 * @return string
		 */
		public static function normalizeIPV6Address($address){
			return trim($address);
		}

		/**
		 * @param $address
		 * @return bool|string
		 */
		public static function normalize($address){
			if(self::isIPV4Address($address)){
				return self::normalizeIPV6Address($address);
			}elseif(self::isIPV6Address($address)){
				return self::normalizeIPV6Address($address);
			}else{
				return false;
			}
		}

		/**
		 * @param $address
		 * @return $this
		 */
		public function setAddress($address){
			if(self::isIPV4Address($address)){
				$this->type = self::TYPE_IPV4;
				$this->address = self::normalizeIPV4Address($address);
			}elseif(self::isIPV6Address($address)){
				$this->type = self::TYPE_IPV6;
				$this->address = self::normalizeIPV6Address($address);
			}else{
				throw new \LogicException('Invalid IP address "'.$address.'"');
			}
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getAddress(){
			return $this->address;
		}

		/**
		 * @return int
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @return mixed
		 */
		public function __toString(){
			return $this->address;
		}

		/**
		 * @return Domain host domain
		 */
		public function getDomain(){
			return Domain::get(gethostbyaddr($this->address));
		}

		/**
		 * @param $ip
		 * @return bool
		 */
		public static function match($ip){
			return filter_var($ip,FILTER_VALIDATE_IP | FILTER_FLAG_IPV6);(bool) preg_match('@[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}@',trim(trim($ip,'.')));
		}

		/**
		 * @param $ip
		 * @return IP|null
		 */
		public static function get($ip){
			if(self::isIPAddress($ip)){
				$i = array_search($ip,self::$ip_collection);
				if($i!==false){
					return self::$ip_collection[$i];
				}else{
					$a = new IP();
					$a->setAddress(self::normalize($ip));
					self::$ip_collection[] = $a;
					return $a;
				}
			}else{
				throw new \LogicException('Passed "'.$ip.'" is not valid ip');
			}
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isIPAddress($address){
			return self::isIPV4Address($address) || self::isIPV6Address($address);
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isIPV4Address($address){
			return (bool)preg_match(self::$ip_v4_regex,$address);
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isIPV6Address($address){
			return (bool)preg_match(self::$ip_v6_regex,$address);
		}

	}
}

