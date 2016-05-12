<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.05.2016
 * Time: 18:25
 */
namespace Jungle\Application {

	/**
	 * Class ResponseBuilder
	 * @package Jungle\Application
	 */
	class ResponseBuilder{

		/**
		 *
		 * Если пришел HTTP запрос
		 * то и ответ будет HTTP
		 *
		 * Если пришел HTTP запрос с Accept-Content-Type: JSON
		 * то и ответ должен быть JSON формата
		 *
		 * Если пришел обычный HTTP запрос то ответ должен быть в формате HTML
		 *
		 *
		 * @param RequestInterface $request
		 */
		public function build(RequestInterface $request){

			/**
			 * HTTP-JSON -> HTTP-JSON
			 * HTTP-HTML -> HTTP-HTML
			 */


		}

	}
}

