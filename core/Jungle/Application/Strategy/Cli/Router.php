<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.07.2016
 * Time: 1:56
 */
namespace Jungle\Application\Strategy\Cli {
	
	use Jungle\Application\RequestInterface;

	/**
	 * Class Router
	 * @package Jungle\Application\Strategy\Cli
	 */
	class Router extends \Jungle\Application\Router{
		
		/**
		 * @param RequestInterface $request
		 * @return mixed
		 */
		public function isDesiredRequest(RequestInterface $request){

		}

	}
}

