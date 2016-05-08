<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 17:10
 */
namespace App\Controllers {
	
	use Jungle\Application\Dispatcher\Context;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

	class UserController{

		/**
		 * @param ProcessInterface $context
		 */
		public function indexAction(ProcessInterface $context){
			echo 'Это Айди Пользователя: '.$context->id;
		}


		public function loginAction(ProcessInterface $context){

		}


		public function logoutAction(ProcessInterface $context){



		}

	}
}

