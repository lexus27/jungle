<?php
namespace test\DataMap;
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.03.2016
 * Time: 4:21
 */


/**
 * Model(Модель) - представление данных в памяти обеспечивающее удобный подход к редактированию/чтению определенного
 * объекта данных.
 * в Jungle, Модели будут определяться не только как субъект ORM .
 * Так-же расчет работы модели не ограничивается на сервере, а должен по требованию представлять себя в виде модели
 * на клиенте (JS) на нативной библиотеке Jungle.
 * В плане видимости Модель может помечатся:
 * Публичной
 *      Модель которая может использоваться на клиенте.
 *
 * Приватной
 *      Модель которая не используется на клиенте.
 *
 *
 * Так-же Поля данных DataMap каждое поле которой помечается соответствующим флагом видимости.
 * Public | Private
 * Поля могут быть read-only, Это может быть актуально как для виртуальных полей, так и для реальных.
 *
 * private
 * public
 * public(read-only)
 * private read-only - Противоречивое понятие определения
 *
 * Представим что модель выглядим следующим образом:
 *
 *  Model{
 *
 *      public read-only $id;
 *
 *      public $name;
 *
 *      public $city;
 *
 *      private $password;
 *
 * }
 *
 */

interface Model{

	public function getSchema();

	/**
	 * так как модели это своего рода DataMap , а для них есть главные коллекции содержания
	 * нужно как-то иметь связь с главной коллекцией, для связи с контекстом коллекций
	 * @return mixed
	 */
	public function getHolderCollection();

	public function getProperty($property_key);

	public function setProperty($property_key, $value);

	public function assign($data);

	/**
	 * Метод должен экспортировать набор полей схемы в массив, значение рекурсивности определяет будут ли
	 * экспортироваться вложеные поля-коллекции и поля содержащие Relation модели если нет то экспортируются только
	 * те поля которые не содержат циклических ссылок (скаляры и  контейнеры скаляров в виде массива)
	 *
	 * метод в общем обладает функционалом конверсии в обычный массив
	 *
	 * @param $field_list
	 * @param bool|false $recursive
	 * @return mixed
	 */
	public function export($field_list, $recursive = false);

	public function __set($key, $value);

	public function __get($key);

	public function __unset($key);

	public function __isset($key);


	public function save();

	public function isNew();

	public function getId();

}

