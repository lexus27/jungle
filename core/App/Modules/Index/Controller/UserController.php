<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 0:46
 */
namespace App\Modules\Index\Controller {
	
	use App\Model\User;
	use Jungle\Application\Dispatcher\Controller\Process;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

	/**
	 * Class UserController
	 * @package App\Modules\Main\Controller
	 */
	class UserController{

		public function indexMeta(array $meta = []){
			$meta['hint'] = [
				'hinting' => [
					'user:'.User::class
				]
			];
			return $meta;

		}

		/**
		 * @param Process $process
		 */
		public function indexAction(Process $process){
			echo '<h1>'.$process->getReferenceString().'</h1>';
			echo '<p><a href="'.$process->getRouter()->generateLinkBy('root').'">Главная</a></p>';
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

