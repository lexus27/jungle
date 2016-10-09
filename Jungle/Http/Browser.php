<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 16:08
 */
namespace Jungle\Http {
	
	use Jungle\Util\Specifications\Http\BrowserInterface;
	use Jungle\Util\Value\UserAgent;

	/**
	 * Class Browser
	 * @package Jungle\Http
	 */
	class Browser implements BrowserInterface{

		/** @var string */
		protected $browser;

		/** @var string */
		protected $version;

		/** @var string */
		protected $platform;


		/** @var  array */
		protected $languages = [];

		/** @var  array */
		protected $media_types = [];

		/** @var  Request */
		protected $request;

		/**
		 * Client constructor.
		 * @param \Jungle\Http\Request $request
		 */
		public function __construct(Request $request){
			$this->request = $request;
			$this->languages = self::parseAcceptLanguage($request->getHeader('Accept-Language','en'));

			if($ua = $this->getUserAgent()){
				$processed = UserAgent::parse($ua);

				$this->browser = $processed['browser'];
				$this->version = $processed['version'];
				$this->platform = $processed['platform'];
			}
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->browser;
		}

		/**
		 * @return string
		 */
		public function getVersion(){
			return $this->version;
		}

		/**
		 * @return string
		 */
		public function getPlatform(){
			return $this->platform;
		}

		/**
		 * @return string
		 */
		public function getUserAgent(){
			return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
		}

		/**
		 * @return bool
		 */
		public function isMobile(){
			return preg_match('@android|ios|ipad|mobile|phone@i',$this->platform)>0;
		}

		/**
		 * @return bool
		 */
		public function isUnknown(){
			return $this->browser===null;
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
		public function getDesiredLanguages(){
			return $this->languages;
		}


		/**
		 * @return mixed
		 */
		public function getBestMediaType(){
			// TODO: Implement getBestMediaType() method.
		}

		/**
		 * @return mixed
		 */
		public function getDesiredMediaTypes(){
			// TODO: Implement getDesiredMediaTypes() method.
		}

		/**
		 * @return mixed
		 */
		public function getBestCharset(){
			// TODO: Implement getBestCharset() method.
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

	}
}

