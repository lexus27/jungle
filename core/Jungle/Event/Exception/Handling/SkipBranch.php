<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.03.2016
 * Time: 13:14
 */
namespace Jungle\Event\Exception\Handling {

	use Jungle\Event\Exception;
	use Jungle\Event\Exception\Handling;

	/**
	 * Досрочное завершение обработки ветки (@see Jungle\Event\Listener\ListenerBranch, @see Jungle\Event\ListenerInterface\Observer with
	 * @see Jungle\Event\Observable\ObservableInterface) веткой называются все
	 * цепочки слушателей которые добавляются посредством метода @see Event::branch
	 * Class SkipBranch
	 * @package Jungle\Event\Exception
	 */
	class SkipBranch extends Handling{}
}

