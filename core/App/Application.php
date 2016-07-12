<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 1:05
 */
namespace App {

	use App\Model\User;
	use Jungle\Application\Adaptee\Http\Dispatcher\Router as HTTP_Router;
	use Jungle\Application\Dispatcher\Router\Binding;
	use Jungle\Application\StaticApplication;
	use Jungle\Application\View\RendererMatcherInterface;
	use Jungle\Application\ViewInterface;
	use Jungle\Data\Storage\Db\Adapter\Pdo\MySQL;
	use Jungle\Di\DiInterface;
	use Jungle\Loader;

	/**
	 * Class Application
	 * @package App
	 */
	class Application extends StaticApplication{

		protected function registerAutoload(Loader $loader){
			require_once dirname($this->getBaseDirname()) . '/Libraries/Twig/lib/Twig/Autoloader.php';
			\Twig_Autoloader::register();
		}

		/**
		 * @param HTTP_Router $router
		 */
		protected function initializeHttpRoutes(HTTP_Router $router){
			$router->removeExtraRight('/');

			$router->any('/',[
				'name' => 'root',
				'reference' => [
					'controller' 	=> 'index',
					'action' 		=> 'index'
				],
				'modify' => false
			]);

			$router->post('/user/login',[
				'name' => 'user-login',
				'reference' => [
					'controller' 	=> 'user',
					'action' 		=> 'login'
				]
			]);

			$userBinding = new Binding(
				function(User $value){      return ['id' => $value->id];            },
				function(array $params){    return User::findFirst($params['id']);  },
				['id']
			);

			$router->any('/{id:int.nozero}',[
				'name'      => 'user-info-short',
				'reference' => 'user:index',
				'default'   => 'user',
				'bindings'  => [
					'user' => $userBinding
				],
			]);

			$router->any('/user/{id:int.nozero}',[
				'name'      => 'user-info',
				'reference' => 'user:index',
				'default'   => 'user',
				'bindings'  => [
					'user' => $userBinding
				],
			]);


			$router->notFound('index:not_found');
		}

		/**
		 * @param DiInterface $di
		 * @return mixed
		 */
		protected function registerDatabases(DiInterface $di){
			$di->setOverlapFrom('mysql',function($di){
				$adapter = new MySQL([
					'host'      => 'localhost',
					'port'      => '3306',
					'dbname'    => 'jungle',
					'username'  => 'root',
					'password'  => ''
				]);
				$adapter->setDialect(new \Jungle\Data\Storage\Db\Dialect\MySQL());
				return $adapter;
			});

		}

		/**
		 * @param ViewInterface $view
		 * @param RendererMatcherInterface $matcher
		 */
		protected function initializeViewRenderers(ViewInterface $view, RendererMatcherInterface $matcher){}

	}
}

