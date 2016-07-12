<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 0:50
 */
namespace Jungle\User {

	/**
	 * Class Session
	 * @package Jungle\User
	 */
	class Session{
		
		/** @var  string */
		protected $id;
		
		/** @var  array */
		protected $data;
		
		/** @var  mixed */
		protected $user_id;
		
		/** @var  SessionManager */
		protected $manager;
		
		public function setAccessedDomains(){
			
		}
		
	}
}

