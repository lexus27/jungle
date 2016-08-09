<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.08.2016
 * Time: 13:46
 */
namespace Jungle\User\OldSession\SignatureProvider {
	
	use Jungle\User\OldSession\SignatureProvider;
	use Jungle\User\OldSession\SignatureProviderInterface;
	use Jungle\User\SessionManagerInterface;

	/**
	 * Class ChainedProvider
	 * @package Jungle\User\OldSession\SignatureProvider
	 */
	class ChainedProvider implements SignatureProviderInterface{

		/** @var  SignatureProvider[] */
		protected $providers = [];

		/** @var  SignatureProvider */
		protected $current_provider;

		/**
		 * @param SignatureProviderInterface $provider
		 * @return $this
		 */
		public function addProvider(SignatureProviderInterface $provider){
			$this->providers[] = $provider;
			return $this;
		}

		/**
		 * @return SignatureProvider
		 */
		public function match(){
			if(!$this->current_provider){
				foreach($this->providers as $type => $provider){
					$signature = $provider->getSignature();
					if($signature){
						$this->current_provider = $provider;
					}
				}
			}
		}

		/**
		 * @return mixed
		 */
		public function generateSignature(){
			// TODO: Implement generateSignature() method.
		}

		/**
		 * @param SessionManagerInterface $sessionManager
		 * @return mixed
		 */
		public function setSessionManager(SessionManagerInterface $sessionManager){
			// TODO: Implement setSessionManager() method.
		}

		/**
		 * @return mixed
		 */
		public function getSignature(){
			// TODO: Implement getSignature() method.
		}

		/**
		 * @param $signature
		 * @return $this
		 */
		public function setSignature($signature){
			// TODO: Implement setSignature() method.
		}

		/**
		 * @return $this
		 */
		public function removeSignature(){
			// TODO: Implement removeSignature() method.
		}
	}
}

