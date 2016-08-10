<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.08.2016
 * Time: 11:38
 */
namespace Jungle\User\Session\SignatureInspector {
	
	use Jungle\User\Session\SignatureInspector;

	/**
	 * Class Composite
	 * @package Jungle\User\Session\SignatureInspector
	 */
	class Composite extends SignatureInspector{

		/** @var   */
		protected $injector;

		/** @var   */
		protected $inspector;

		/**
		 * @return mixed
		 */
		public function getSignature(){
			// TODO: Implement getSignature() method.
		}

		/**
		 * @param $signature
		 * @param $lifetime
		 * @return $this
		 */
		public function setSignature($signature, $lifetime = null){
			// TODO: Implement setSignature() method.
		}

		/**
		 * @return mixed
		 */
		public function generateSignature(){
			// TODO: Implement generateSignature() method.
		}

	}
}

