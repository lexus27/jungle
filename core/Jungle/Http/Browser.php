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

		/** @var bool */
		protected $processed = false;

		/** @var string */
		protected $browser;

		/** @var string */
		protected $version;

		/** @var string */
		protected $platform;

		/**
		 * Browser constructor.
		 */
		public function __construct(){
			$processed = UserAgent::parse($_SERVER['HTTP_USER_AGENT']);
			$this->browser = $processed['browser'];
			$this->version = $processed['version'];
			$this->platform = $processed['platform'];
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
			return $_SERVER['HTTP_USER_AGENT'];
		}

		/**
		 * @return bool
		 */
		public function isMobile(){
			return preg_match('@android|ios|ipad|mobile|phone@i',$this->platform)>0;
		}


	}
}

