<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 23:24
 */
namespace Jungle\Application {
	
	use Jungle\Di\Injectable;

	/**
	 * Class Component
	 * @package Jungle\Application
	 * Класс компонент(Сервис), которым пользуются вызвав Di->getShared($serviceKey) Di->get($serviceKey)
	 * Пример: Контроллер не является компонентом т.к он не присваивается как сервис Di контейнеру
	 */
	abstract class Component extends Injectable{}
}

