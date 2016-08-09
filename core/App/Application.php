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
	use App\Services\Session\MyModels;
	use Jungle\Application\Dispatcher;
	use Jungle\Application\Router\Binding;
	use Jungle\Application\StaticApplication;
	use Jungle\Application\Strategy\Http;
	use Jungle\Application\View\Renderer\Data\Json;
	use Jungle\Application\View\Renderer\TemplateEngine\Php;
	use Jungle\Application\View\ViewStrategy;
	use Jungle\Application\ViewInterface;
	use Jungle\Data\Storage\Db\Adapter\Pdo\MySQL;
	use Jungle\Di\DiInterface;
	use Jungle\Loader;
	use Jungle\User\AccessControl\Adapter\PolicyAdater\Memory;
	use Jungle\User\AccessControl\Manager;
	use Jungle\User\Account;
	use Jungle\User\Session\Provider\Session;
	use Jungle\User\Session\SignatureInspector\Cookies;
	use Jungle\User\SessionManager;
	use Jungle\Util\Specifications\Http\RequestInterface;

	/**
	 * Class Application
	 * @package App
	 */
	class Application extends StaticApplication{

		/**
		 * @param DiInterface $di
		 * @return mixed
		 */
		protected function registerServices(DiInterface $di){
			$policyPool = new Memory();
			$access = new Manager();
			$access->setPolicyAdapter($policyPool);
			$di->setShared('access',$access);
		}

		/**
		 * @return ViewInterface
		 */
		protected function initializeView(){
			$view  = parent::initializeView();
			$view->setVar('services',$this->_dependency_injector);
			return $view;
		}

		/**
		 * @param ViewInterface $view
		 *
		 * Define method with pattern: initialize[StrategyAlias][RendererAlias]RendererRule($strategy, $view)
		 *
		 */
		protected function initializeViewRenderers(ViewInterface $view){
			$view->setRenderer('htmlMobile', new Php('text/html'));
			$view->setRenderer('html', new Php('text/html'));
			$view->setRenderer('json', new Json('application/json'));
		}

		/**
		 * @return ViewStrategy\RendererRule
		 */
		protected function initializeHttpHtmlMobileRendererRule(){
			$rule = new ViewStrategy\RendererRule(function(RequestInterface $request, $process, $view){
				return stripos($request->getHeader('Accept'),'html')!==false && $request->getBrowser()->isMobile();
			});
			return $rule;
		}

		/**
		 * @param DiInterface $di
		 */
		protected function initializeHttpServices(DiInterface $di){

			$di->setShared('session',function(DiInterface $di){

				$manager = new SessionManager();
				$manager->setDi($this->_dependency_injector);
				$manager->setStorage(new MyModels());

				$provider = new Session();
				$provider->setDi($this->_dependency_injector);
				$provider->setSignatureInspector( (new Cookies())->setCookieName('JUNGLE_SESS') );

				$manager->setDefaultProvider($provider);
				$manager->setLifetime(86000 * 2);

				return $manager;
			});

			$di->setShared('cookie', new Http\CookieManager());

			$di->setShared('account', new Account());

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
		 * @param Http\Router $router
		 */
		protected function initializeHttpRoutes(Http\Router $router){
			$router->removeExtraRight('/');

			$router->any('/',[
				'main' => true,
				'name' => 'root',
				'reference' => [
					'controller' 	=> 'index',
					'action' 		=> 'index'
				],
				'modify' => false
			]);

			$router->any('/auth',[
				'name'      => 'user-login',
				'reference' => 'user.auth:login',
			    'converter' => function($p,RequestInterface $request){
				    if($request->isPost()){
					   $p['login'] = $request->getPost('login');
					   $p['password'] = $request->getPost('password');
				    }
				    return $p;
			    }
			]);


			$router->any('/logout',[
				'name'      => 'user-logout',
				'reference' => 'user.auth:logout'
			]);


			$userBinding = new Binding(
				function(User $value){      return ['id' => $value->id];            },
				function(array $params){    return User::findFirst($params['id']);  },
				['id']
			);

			$router->any('/{username:string:([^/]+)}',[
				'name'      => 'user-short',
				'reference' => 'user:index',
				'default'   => 'user',
				'bindings'  => [
					'user' => new Binding(
						function(User $value){      return ['username' => $value->username];            },
						function(array $params){
							$user = User::findFirst([['username','=',$params['username']]]);
							if(!$user){
								throw new Dispatcher\Exception\ContinueRoute;
							}
							return $user;
						},
						['username']
					)
				],
			]);

			$router->any('/user/{id:int.nozero}',[
				'name'      => 'user',
				'reference' => 'user:index',
				'default'   => 'user',
				'bindings'  => [
					'user' => $userBinding
				],
			]);

			$router->any('/user/create',[
				'name'      => 'user-create',
				'reference' => 'user:create',
				'converter' => function($p,RequestInterface $request){
					if($request->isPost()){
						$p['username'] = $request->getPost('username');
						$p['password'] = $request->getPost('password');
						$p['password_confirm'] = $request->getPost('password_confirm');
					}
					return $p;
				},
			]);

			$router->any('/user/{id:int.nozero}/update',[
				'name'      => 'user-update',
				'reference' => 'user:update',
				'default'   => 'user',
				'converter' => function($p,RequestInterface $request){
					if($request->isPost()){
						$p['username'] = $request->getPost('username');
					}
					return $p;
				},
				'bindings'  => [
					'user' => $userBinding
				],
			]);

			$router->any('/user/{id:int.nozero}/delete',[
				'name'      => 'user-delete',
				'reference' => 'user:delete',
				'default'   => 'user',
				'bindings'  => [
					'user' => $userBinding
				],
			]);




			$noteBinding = new Binding(
				function(User\Note $value){      return ['id' => $value->id];            },
				function(array $params){    return User\Note::findFirst($params['id']);  },
				['id']
			);

			$router->any('/user/note/{id:int.nozero}',[
				'name'      => 'user-note',
				'reference' => 'user:note',
				'default'   => 'note',
				'bindings'  => [
					'note' => $noteBinding
				],
			]);

			$router->any('/user/{id:int.nozero}/notes',[
				'name'      => 'user-notes',
				'reference' => 'user:notes',
				'default'   => 'user',
				'bindings'  => [
					'user' => $userBinding
				],
			]);

			$router->any('/user/{id:int.nozero}/note/create',[
				'name'      => 'user-note-create',
				'reference' => 'user.note:create',
				'default'   => 'user',
				'converter' => function($p,RequestInterface $request){
					if($request->isPost()){
						$p['title'] = $request->getPost('title');
						$p['text'] = $request->getPost('text');
					}
					return $p;
				},
				'bindings'  => [
					'user' => $userBinding,
				],
			]);

			$router->any('/user/note/{id:int.nozero}/update',[
				'name'      => 'user-note-update',
				'reference' => 'user.note:update',
				'default'   => 'note',
				'converter' => function($p,RequestInterface $request){
					if($request->isPost()){
						$p['title'] = $request->getPost('title');
						$p['text']  = $request->getPost('text');
					}
					return $p;
				},
				'bindings'  => [
					'note' => $noteBinding,
				],
			]);

			$router->any('/user/note/{id:int.nozero}/comment',[
				'name'      => 'user-note-comment-create',
				'reference' => 'user.comment:create',
				'default'   => 'subject',
				'converter' => function($p,RequestInterface $request){
					if($request->isPost()){
						$p['text'] = $request->getPost('text');
						$uId = $this->session->user_id;
						$p['user'] = $uId?User::findFirst($uId):null;
					}
					return $p;
				},
				'bindings'  => [
					'subject' => $noteBinding,
				],
			]);

			$router->any('/user/note/{id:int.nozero}/delete',[
				'name'      => 'user-note-delete',
				'reference' => 'user.note:delete',
				'default'   => 'note',
				'bindings'  => [
					'note' => $noteBinding,
				],
			]);

			$router->notFound('index:not_found');
		}


		/**
		 * @param Loader $loader
		 */
		protected function registerAutoload(Loader $loader){}
	}
}

