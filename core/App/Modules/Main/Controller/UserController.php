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
	
	use Jungle\Application\Dispatcher\Controller\Process;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

	class UserController{

		public function indexAction(Process $process){

			echo $process->id;

			echo '<h1>'.$process->getReferenceString().'</h1>';

			echo '<p><a href="'.$process->getRouter()->generateLinkBy(
					'user-info',
					['id' => $process->id + 10],
					'user:index'
				).'">user:index(normal)</a></p>';

			echo '<p><a href="'.$process->getRouter()->generateLinkBy(
					'user-info-short',
					['id' => $process->id + 80],
					'user:index'
				).'">user:index(short)</a></p>';


			echo '<p><a href="'.$process->getRouter()->generateLinkBy('root').'">index:index</a></p>';

		}

		/**
		 * @param ProcessInterface $process
		 * @return mixed
		 */
		public function infoActon(ProcessInterface $process){
			return $process->user;
		}

		/**
		 * @param Process $process
		 * @param $p
		 * @param $reference
		 */
		protected function renderLink(Process $process, $p , $reference, $name = null){
			if($name){
				$link = $process->getRouting()->getRoute()->getRouter()->generateLinkBy($name, $p , $reference);
			}else{
				$link = $process->getRouting()->getRoute()->getRouter()->generateLink($p , $reference);
			}

			echo '<p><a href="'.$link.'">Перейти по ссылке: "http://'.$_SERVER['HTTP_HOST'] . $link.'"</a></p>';
		}
	}
}

