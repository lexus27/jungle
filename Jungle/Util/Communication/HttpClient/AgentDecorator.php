<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.10.2016
 * Time: 0:09
 */
namespace Jungle\Util\Communication\HttpClient {

	/**
	 * Class AgentDecorator
	 * @package Jungle\Util\Communication\HttpClient
	 */
	class AgentDecorator extends Agent{

		/** @var Agent  */
		protected $agent;

		/**
		 * AgentDecorator constructor.
		 * @param Agent $agent
		 */
		public function __construct(Agent $agent){
			$this->agent = $agent;
		}

		public function getUserAgent(){
			return $this->user_agent!==null?
				$this->user_agent :
				$this->agent->getUserAgent();
		}

		public function getBestLanguage(){
			return $this->desired_languages!==null?
				current($this->desired_languages) :
				$this->agent->getBestLanguage();
		}

		public function getDesiredLanguages(){
			return $this->desired_languages!==null?
				$this->desired_languages :
				$this->agent->getDesiredLanguages();
		}

		public function getBestMediaType(){
			return $this->desired_media_types!==null?
				current($this->desired_media_types) :
				$this->agent->getBestMediaType();
		}

		public function getDesiredMediaTypes(){
			return $this->desired_media_types!==null?
				$this->desired_media_types :
				$this->agent->getDesiredMediaTypes();
		}

		public function getBestCharset(){
			return $this->desired_charsets!==null?
				current($this->desired_charsets) :
				$this->agent->getBestCharset();
		}

		public function getDesiredCharsets(){
			return $this->desired_charsets!==null?
				$this->desired_charsets :
				$this->agent->getDesiredCharsets();
		}

		public function getDefaultHeaders(){
			return $this->default_headers!==null?
				array_replace($this->agent->getDefaultHeaders(), $this->default_headers) :
				$this->agent->getDefaultHeaders();
		}

		/**
		 * @return Network
		 */
		public function getNetwork(){
			return $this->network?: $this->agent->getNetwork();
		}


	}
}

