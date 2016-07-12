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
	
	use App\Model\User;
	use Jungle\Application\Dispatcher\Controller\Process;
	use Jungle\Application\View\Renderer\Template;
	use Jungle\Di\Injectable;


	/**
	 * Class IndexController
	 * @package App\Modules\Index\Controller
	 */
	class IndexController extends Injectable{

		/**
		 * @param array $options
		 * @return array
		 */
		public function getDefaultMeta(array $options = []){
			return array_replace($options,[
				'private'       => false,
				'hierarchy'     => true,
				'independent'   => true
			]);
		}

		/**
		 *
		 */
		public function initialize(){

		}


		/**
		 * @param array $options
		 * @return array
		 */
		public function indexMeta(array $options = []){
			return array_replace($options,[
				'private'       => true,
				'hierarchy'     => true,
			    'native_render' => ['html'],
			]);
		}

		public function not_foundAction(Process $process){

		}

		/**
		 * @param Process $process
		 */
		public function indexAction(Process $process){

			echo '<h1>Hello, This is <i style="color: darkgreen;font-size: 48px;">Jungle Test Application!</i></h1>';
			echo '<h1>'.$process->getReferenceString().'</h1>';
			foreach(User::find() as $user){
				echo '<p><a href="'.$this->router->generateLinkBy('user-info-short', $user).'">'.$user->username.'</a></p>';
			}

			/**
			 * Specify HTTP
			 */
			//$this->response->setContentType('application/json');
			//$this->response->setContent(json_encode($this->request->getQuery()));
			//$this->response->setCookie('my_cookie',[1,2,3,5]);


			echo $process->call('user.auth:index')->getOutputBuffer();
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

