<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.10.2016
 * Time: 13:12
 */
namespace Jungle\Util\Communication\Http\Agent {

	/**
	 * Class Context
	 * @package Jungle\Util\Communication\Http
	 *
	 * Запросы Могут отправлять через контекст,
	 * контекст представляет служебную подстановку заголовков перед отправкой,
	 * тоесть через контекст происходит часть подготовки запроса
	 *
	 */
	class Context{

		protected $desired_media_types = [];

		protected $cache;


		/**
		 * @param Request $request
		 * @return void
		 */
		public function visit(Request $request){

		}

	}
}

