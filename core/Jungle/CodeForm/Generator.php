<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.03.2016
 * Time: 2:34
 */
namespace Jungle\CodeForm {


	/**
	 * Class CodeGenerator
	 * @package Jungle
	 *
	 * TODO implements
	 *
	 */
	class Generator{

		/**
		 * TODO implement
		 * Экспортирование значения для разных Языков программирования разное,
		 *
		 * особенно когда значение является производным какого-то класса
		 *
		 * Export пишет вызов толи - Builder толи New производного класса чтобы получить объект похожий на переданный в этот метод
		 *
		 * Пример(не относится к реалиям)
		 * Менюшка на клиенте ей нужна коллекция итемов на пример для JS обработчиков:
		 *
		 *      Collection -
		 *      Schema:
		 *          [{id},{name},{controller_name}]
		 *      Items:
		 *          [1,'Главная',['web','index','index']]
		 *          [2,'Товары',['web','index','products']]
		 *          [3,'Написать нам',['web','index','write']]
		 *          [4,'О компании',['web','index','about']]
		 *
		 *
		 *
		 * на выходе имеем что-то вроде:
		 *      new Collection([{id},{name},{controller_name}], [
		 *          [1,'Главная',['web','index','index']],
		 *          [2,'Товары',['web','index','products']],
		 *          [3,'Написать нам',['web','index','write']],
		 *          [4,'О компании',['web','index','about']]
		 *      ]);
		 *
		 * @param $value
		 */
		public function export($value){

		}

		/**
		 * @param $value
		 *
		 * Привести значение в строку сериализованную в формат читаемый языком программирования
		 *
		 */
		public function serialize($value){

		}

	}
}

