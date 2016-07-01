<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 0:48
 */
namespace App\Modules\Index\Controller {
	
	use Jungle\Application\Dispatcher\Controller\Process;


	/**
	 * Class IndexController
	 * @package App\Modules\Index\Controller
	 */
	class IndexController{

		/**
		 *
		 */
		public function getPrivateActions(){
			
		}

		/**
		 *
		 */
		public function getHierarchyActions(){
			
		}

		/**
		 *
		 */
		public function getIndependentActions(){

		}

		/**
		 *
		 */
		public function initialize(){

		}

		/**
		 * @param Process $process
		 */
		public function indexAction(Process $process){
			echo '<h1>Hello, This is <i style="color: darkgreen;font-size: 48px;">Jungle Test Application!</i></h1>';
			echo '<h1>'.$process->getReferenceString().'</h1>';
			echo '<p><a href="'.$process->getRouter()->generateLinkBy(
					'user-info',
					['id' => 34],
					'user:index'
				).'">user:index(normal)</a></p>';
			echo '<p><a href="'.$process->getRouter()->generateLinkBy(
					'user-info-short',
					['id' => 81],
					'user:index'
				).'">user:index(short)</a></p>';
			$process->call('user.auth:index');
		}
		
		/**
		 * @param $type
		 * @param $result
		 * @return bool
		 */
		public function indexRender($type, $result){
			return false;
		}

	}
}

