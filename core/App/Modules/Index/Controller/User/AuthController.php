<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 0:49
 */
namespace App\Modules\Index\Controller\User {
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

	/**
	 * Class AuthController
	 * @package App\Modules\Index\Controller\User
	 */
	class AuthController{

		/**
		 *
		 */
		public function initialize(){

		}

		/**
		 * 
		 */
		public function indexBeforeControl(){

			echo 'BeforeControlIndex';

		}

		public function indexAction(ProcessInterface $process){
			echo get_class($this);
		}


		public function indexAfterControl(){

			echo 'AfterControlIndex';

		}
	}
}

