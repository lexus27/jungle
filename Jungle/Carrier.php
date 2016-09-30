<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.09.2016
 * Time: 22:02
 */
namespace Jungle {
	
	/**
	 * Class Carrier
	 * @package Jungle
	 *
	 * Переносчик параметров, для глобального или локального подмешивания и использования в
	 * ссылках специальных параметров используемых
	 * в подключаемых компонентах приложения
	 * Здесь для того чтобы вставить параметры нам нужна ссылка или структура ссылки,
	 */
	class Carrier{

		/**
		 * @param $key
		 * @param $value
		 */
		public function setParam($key, $value){
			
		}

		/**
		 * @param $key
		 */
		public function getParam($key){
			
		}

		/**
		 * @param $key
		 */
		public function hasParam($key){
			
		}

		/**
		 * @param $link
		 */
		public function instructLink($link){

		}

	}
}

