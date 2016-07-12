<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 2:07
 */
namespace Jungle\Util\Communication\Http {

	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\ServerInterface;

	/**
	 * Class Manager
	 * @package Jungle\Util\Communication\Http
	 */
	class Manager{

		/** @var array  */
		protected $requests = [];

		protected $default_agent;

		/**
		 * @param $user_agent
		 * @return $this
		 */
		public function setDefaultAgent($user_agent){
			$this->default_agent = $user_agent;
			return $this;
		}

		public function addRequest(RequestInterface $request, ServerInterface $server){

		}
		
		public function execute(){
			
		}

	}
}

