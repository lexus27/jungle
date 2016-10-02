<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 13:45
 */
namespace Jungle\Util\Communication\Integrator {

	use Jungle\Util\Communication\Http\Request;

	/**
	 * Class RequestManager
	 * @package Jungle\Util\Communication\Integrator
	 */
	class RequestManager{

		/**
		 * @return Request
		 */
		public function newRequest(){
			return new Request();
		}


		/**
		 * @param Request $request
		 * @param callable $onResponse
		 */
		public function sendRequest(Request $request, callable $onResponse){

		}

	}
}

