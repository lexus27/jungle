<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.04.2016
 * Time: 23:52
 */
namespace Jungle\Di {

	/**
	 * Interface DiInterface
	 * @package Jungle\Di
	 *
	 * Объединяющий интерфейс обобщения
	 *
	 */
	interface DiInterface extends DiLocatorInterface, DiNestingInterface, \ArrayAccess{}

}

