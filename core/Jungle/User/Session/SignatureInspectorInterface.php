<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 21:00
 */
namespace Jungle\User\Session {

	/**
	 *
	 * Interface SignatureInspectorInterface
	 * @package Jungle\User\Session
	 */
	interface SignatureInspectorInterface{

		/**
		 * @return mixed
		 */
		public function getSignature();

		/**
		 * @param $signature
		 * @param $lifetime
		 * @return $this
		 */
		public function setSignature($signature, $lifetime = null);



		/**
		 * @return bool
		 */
		public function hasSignal();



		/**
		 * @return mixed
		 */
		public function generateSignature();

	}

}

