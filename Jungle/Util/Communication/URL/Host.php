<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.01.2016
 * Time: 20:13
 */
namespace Jungle\Util\Communication\URL {

	use Jungle\Util\Communication\URL\Host\Domain;
	use Jungle\Util\Communication\URL\Host\IP;
	use Jungle\Util\Smart\Value\Value;

	/**
	 * Class Address
	 * @package Jungle\Util\Communication
	 *
	 * IP or DomainName
	 *
	 */
	class Host extends Value{

		const PRIORITY_IP = 1;

		const PRIORITY_DOMAIN = 2;

		/**
		 * @var Domain|null
		 */
		protected $domain;

		/**
		 * @var IP|null
		 */
		protected $ip;

		/**
		 * @var int
		 */
		protected $render_priority = self::PRIORITY_DOMAIN;


		/**
		 * @param int $default
		 * @return $this
		 */
		public function setRenderPriority($default = self::PRIORITY_DOMAIN){
			$this->render_priority = $default;
			return $this;
		}

		/**
		 * @param $ip
		 * @return $this
		 */
		public function setIP($ip){
			$this->ip = IP::get($ip);
			$this->domain = null;
			return $this;
		}

		/**
		 * @return IP
		 */
		public function getIP(){
			if(!$this->ip && $this->domain){
				$this->ip = $this->domain->getIP();
			}
			return $this->ip;
		}

		/**
		 * @param $domain
		 * @return $this
		 */
		public function setDomain($domain){
			$this->ip = null;
			$this->domain = Domain::get($domain);
			return $this;
		}

		/**
		 * @return Domain
		 */
		public function getDomain(){
			if(!$this->domain && $this->ip){
				$this->domain = $this->ip->getDomain();
			}
			return $this->domain;
		}


		/**
		 * @return bool
		 */
		public function isSubDomain(){
			$domain = $this->getDomain();
			return $domain?$domain->isSubDomain():false;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			switch($this->render_priority){
				default://case self::PRIORITY_DOMAIN:
					$domain = $this->getDomain();
					return (string)($domain?:$this->getIP());
					break;
				case self::PRIORITY_IP:
					$ip = $this->getIP();
					return (string)($ip?:$this->getDomain());
					break;
			}
		}

		/**
		 * @param $host
		 * @return Host
		 */
		public static function get($host){
			if(self::isHost($host)){
				$h = new Host();
				if(self::isDomain($host)){
					$h->setDomain($host);
				}
				if(self::isIPAddress($host)){
					$h->setIP($host);
				}
				return $h;
			}
			return $host instanceof Host?$host:null;
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isHost($address){
			return self::isDomain($address) || self::isIPAddress($address);
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isIPAddress($address){
			return IP::isIPAddress($address);
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isIPV4Address($address){
			return IP::isIPV4Address($address);
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isIPV6Address($address){
			return IP::isIPV6Address($address);
		}

		/**
		 * @param $address
		 * @return bool
		 */
		public static function isDomain($address){
			return Domain::isDomain($address);
		}

	}
}

