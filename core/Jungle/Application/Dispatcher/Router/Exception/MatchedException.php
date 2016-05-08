<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.05.2016
 * Time: 16:06
 */
namespace Jungle\Application\Dispatcher\Router\Exception {
	
	use Jungle\Application\Dispatcher\Router\Exception;
	use Jungle\Application\Dispatcher\Router\Routing;
	use Jungle\Application\Dispatcher\Router\RoutingInterface;

	/**
	 * Class MatchedException
	 * @package Jungle\Application\Dispatcher\Router\Exception
	 */
	class MatchedException extends Exception{

		/** @var  Routing */
		protected $result;

		/**
		 * MatchedException constructor.
		 * @param RoutingInterface $result
		 */
		public function __construct(RoutingInterface $result){
			$this->result = $result;
			parent::__construct();
		}

		/**
		 * @return Routing
		 */
		public function getResult(){
			return $this->result;
		}

	}
}

