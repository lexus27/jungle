<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.07.2016
 * Time: 22:15
 */
namespace Jungle\Application {

	/**
	 * Class PublicInput
	 * @package Jungle\Application
	 */
	class PublicInput{

		/**
		 * @param $key
		 * @return mixed
		 *
		 * Типо получаем нужный сортировщик
		 * В общем требуется решение:
		 * Как работать с компонентами, без взаимодействия с типом запроса из контроллера:
		 * Например :
		 *      Пагинация    (для запросов).
		 *      Сортировщики (для запросов),
		 *      Критерии     (для запросов)
		 *
		 */
		public function getContainer($key){

		}

	}
}

