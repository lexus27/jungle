<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.01.2017
 * Time: 17:37
 */
namespace Jungle\Data\Record\Locator {

	/**
	 * Interface Locator
	 * @package Jungle\Data\Record\Locator
	 */
	interface Locator{

		public function getPrevSchema();

		public function getOpeningSchema();

		public function getSchema();

		public function getReversedPath();

		public function isMany();

		public function hasMany();
		public function hasManyReversed();
		public function hasHost();

		public function isLocal();

		public function isRecursive();

		public function isCircular();
		
		public function hasReversed();
		
		public function getPrevPath($default_path = null);

		/**
		 * Метод для сбора всех поинтов в линию, с возможностью собирателя
		 * @param bool|false $reversed - Направление выдачи
		 *      @TRUE - Поинты с конца до начала
		 *      @FALSE - Поинты от Начала до конца (по порядку от начала пути)
		 *
		 * @param null $collector - Сборщик, который соберает какие-то данные на этапе сбора самих путей
		 *                          Вызовится для каждого поинта (По умолчанию от конца к началу)
		 *      @callable - Вызов калбека($point)
		 *      @string - Вызов метода для поинта, по названию которое заключенео в строке
		 *      @array - Индексный массив, первый элемент - название метода, остальные - аргументы для метода
		 *
		 * @param bool $collector_ordered - Сборщик вызывается строго по указаному
		 *                                  направлению в $reversed (Например для замыканий со ссылками)
		 *
		 * @return Point[]|array
		 */
		public function line($reversed = false, $collector = null, $collector_ordered = false);

	}
}

