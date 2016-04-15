<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.11.2015
 * Time: 11:31
 */
namespace Jungle\Basic {

	/**
	 * Interface INamedRead
	 * @package Jungle\Basic
	 */
	interface INamedRead{

		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName();

	}
}

