<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.06.2016
 * Time: 0:12
 */
namespace Jungle\Http {

	use Jungle\Util\Specifications\Http\ClientInterface;

	/**
	 * Class Client
	 * @package Jungle\HTTPFoundation
	 */
	class Client implements ClientInterface{

		/** @var  string */
		protected $ip;

		/** @var  int */
		protected $port;

		/** @var  string[] */
		protected $languages = [];

		/** @var  Request */
		protected $request;

		/**
		 * Client constructor.
		 * @param \Jungle\Http\Request $request
		 */
		public function __construct(Request $request){
			$this->request = $request;
			$this->languages = self::parseAcceptLanguage($request->getHeader('Accept-Language','en'));
		}

		/**
		 * @param $subject
		 * @return array
		 */
		public static function parseAcceptLanguage($subject){
			$subject = explode(';',$subject);
			$languages = [];
			foreach($subject as $lang){
				if(preg_match('/((([a-zA-Z]+)(-[a-zA-Z]+)?)|\*)/',$lang,$matches)){
					if(isset($matches[3]) && $matches[3]){
						$value = strtolower($matches[3]);
						if(!in_array($value,$languages, true)){
							$languages[] = $value;
						}
					}
				}
			}
			return $languages;
		}

		/**
		 * @return string
		 */
		public function getIp(){
			return $_SERVER['REMOTE_ADDR'];
		}

		/**
		 * @return string
		 */
		public function getHost(){
			return $_SERVER['REMOTE_HOST'];
		}

		/**
		 * @return int
		 */
		public function getPort(){
			return $_SERVER['REMOTE_PORT'];
		}

		/**
		 * @return string
		 */
		public function getBestLanguage(){
			return !empty($this->languages)?$this->languages[0]:'en';
		}

		/**
		 * @return string[]
		 */
		public function getLanguages(){
			return $this->languages;
		}

		public function isProxied(){
			// TODO: Implement isProxied() method.
		}

		public function getProxy(){
			// TODO: Implement getProxy() method.
		}

	}
}

