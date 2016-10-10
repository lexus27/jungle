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
	
	use Jungle\Application\Strategy\Http;
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



		protected $desired_media_types;

		protected $desired_languages;

		protected $desired_charsets;





		/**
		 * Client constructor.
		 * @param \Jungle\Http\Request $request
		 */
		public function __construct(Request $request){
			$this->request = $request;

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
		 * @return string|null
		 */
		public function getBestLanguage(){
			$items = $this->getDesiredLanguages();
			return !empty($items)?$items[0]:null;
		}

		/**
		 * @return array
		 */
		public function getDesiredLanguages(){
			if($this->desired_languages === null){
				if(!$_SERVER['HTTP_ACCEPT_LANGUAGE']){
					return null;
				}
				$this->desired_languages = \Jungle\Util\Communication\Http::parseAccept($_SERVER['HTTP_ACCEPT_LANGUAGE']);
			}
			return $this->desired_languages;
		}


		/**
		 * @return string|null
		 */
		public function getBestMediaType(){
			$items = $this->getDesiredMediaTypes();
			return !empty($items)?$items[0]:null;
		}

		/**
		 * @return array
		 */
		public function getDesiredMediaTypes(){
			if($this->desired_media_types === null){
				if(!$_SERVER['HTTP_ACCEPT']){
					return null;
				}
				$this->desired_media_types = \Jungle\Util\Communication\Http::parseAccept($_SERVER['HTTP_ACCEPT']);
			}
			return $this->desired_media_types;
		}

		/**
		 * @return string|null
		 */
		public function getBestCharset(){
			$items = $this->getDesiredCharsets();
			return !empty($items)?$items[0]:null;
		}

		/**
		 * @return array
		 */
		public function getDesiredCharsets(){
			if($this->desired_charsets === null){
				if(!$_SERVER['HTTP_ACCEPT_CHARSET']){
					return null;
				}
				$this->desired_charsets = \Jungle\Util\Communication\Http::parseAccept($_SERVER['HTTP_ACCEPT_CHARSET']);
			}
			return $this->desired_charsets;
		}

	}
}

