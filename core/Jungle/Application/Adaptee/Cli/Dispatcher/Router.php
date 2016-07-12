<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.07.2016
 * Time: 1:56
 */
namespace Jungle\Application\Adaptee\Cli\Dispatcher {
	
	use Jungle\Application\RequestInterface;

	class Router extends \Jungle\Application\Dispatcher\Router{
		
		/**
		 * @param RequestInterface $request
		 * @return mixed
		 */
		public function isDesiredRequest(RequestInterface $request){

		}

	}
}

