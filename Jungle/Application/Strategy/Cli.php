<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.07.2016
 * Time: 23:19
 */
namespace Jungle\Application\Strategy {
	
	use Jungle\Application\RequestInterface;
	use Jungle\Application\Strategy;
	use Jungle\Application\Strategy\Cli\Router as CLI_Router;
	use Jungle\Cli\Request as CLI_Request;

	/**
	 * Class Cli
	 * @package Jungle\Application\Strategy
	 */
	abstract class Cli extends Strategy{

		/** @var string */
		protected $name = 'cli';

		/**
		 * @param RequestInterface $request
		 * @return bool
		 */
		public static function check(RequestInterface $request){
			return $request instanceof CLI_Request;
		}

		/**
		 * @return mixed
		 */
		public function initialize(){
			$this->setShared('router', new CLI_Router());
		}


		/**
		 * @param $type
		 * @return mixed
		 */
		public function setName($type){
			// TODO: Implement setName() method.
		}
	}
}

