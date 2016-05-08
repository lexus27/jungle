<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 0:46
 */
namespace App\Modules\Main\Controller {
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

	class User{

		public function indexAction(){

		}

		/**
		 * @param ProcessInterface $process
		 * @return mixed
		 */
		public function infoActon(ProcessInterface $process){
			return $process->user;
		}

	}
}

