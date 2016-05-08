<?php
namespace App\Controllers;
use Jungle\Application\Dispatcher\Context;
use Jungle\Application\Dispatcher\Controller\ProcessInterface;
use Jungle\HTTPFoundation\RequestInterface;

/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 16:21
 */
class IndexController{

	/**
	 * @param ProcessInterface $process
	 */
	public function indexAction(ProcessInterface $process){

		echo $process->getRouting()->getRoute()->getName();

		if($process->isExternal()){
			$request = $process->getInitiator()->getRequest();
			if($request instanceof RequestInterface){
				echo $request->getQueryParameter('p');
			}else{
				echo 'Hello word';
			}
		}





		/*
		$router = $process->getRoute()->getRouter();
		echo '<a href="'.$router->generateLinkBy('user-info',['id'=> 123213213424]).'">Жми</a><br/>';
		echo '<a href="'.$router->generateLinkBy('user-info-short',['id'=> 123213213424]).'">Жми</a><br/>';
		echo '<a href="'.$router->generateLink(['controller' => 'index','action' => 'index']).'">Жми</a><br/>';
		*/
	}

	/**
	 * @param ProcessInterface $context
	 */
	public function userAction(ProcessInterface $context){
		echo $context->id;
	}


}

