<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.03.2016
 * Time: 14:38
 */
namespace Jungle\Event {

	use Jungle\Event\Observable\ObservableTrait;

	/**
	 * Class ObservableInterface
	 * @package Jungle\Event
	 */
	class Observable implements ObservableInterface{
		use ObservableTrait;
	}
}
