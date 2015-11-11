<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 17:19
 */

namespace Jungle\Basic {

	/**
	 * Interface INamedBase
	 * @package Jungle\Basic
	 *
	 * Базовый интерфейс для именованых объектов
	 *
	 */
	interface INamedBase{

		/**
		 * Получить имя объекта
		 * @return mixed
		 */
		public function getName();

		/**
		 * Выставить имя объекту
		 * @param $name
		 */
		public function setName($name);

	}
}