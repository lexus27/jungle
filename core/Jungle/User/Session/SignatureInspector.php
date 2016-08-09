<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 21:01
 */
namespace Jungle\User\Session {

	use Jungle\Di\Injectable;

	/**
	 * Class SignatureInspector
	 * @package Jungle\User\Session
	 */
	abstract class SignatureInspector extends Injectable implements SignatureInspectorInterface{

		/**
		 * @return string
		 */
		public function generateSignature(){
			return uniqid('ssid_',true);
		}

	}

}

