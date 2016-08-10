<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 17:26
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {

	/**
	 * Interface ISortManager
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools
	 * @TODO TASK нужно проработать сортировщики и их использование в коллекциях
	 * У коллекции есть метод sort, по нему сортируется содержимое коллекции (по заданому по умолчанию критерию)
	 * Сортер это пред-определенный объект который обрабатывает массив по ссылке и отдает отсортированый
	 * Сортер может настраиваться в каком порядке сортировать ASC DESC
	 * Есть пул сортеров указаных по ключам.
	 *
	 * В коллекцию статически был интегрирован пул сортеров, метод sort научился читать строки
	 * и по этой строке с пула берется сортер которым сортируется коллекция
	 *
	 * Параметры сортировки
	 * ASC DESC и детализация полей если каждый элемент коллекции это объект DataMap или обычный Assoc массив
	 *
	 *
	 */
	interface ISorter{

		const SORT_TYPE_ASC     = 'ASC';

		const SORT_TYPE_DESC    = 'DESC';

		/**
		 * @param string $type
		 * @return $this
		 */
		function setDirection($type = self::SORT_TYPE_ASC);

		/**
		 * @param array $array
		 * @return mixed
		 */
		function sort(array & $array);

		/**
		 * @param callable $sorter
		 * @return mixed
		 */
		function setCmp(callable $sorter = null);

	}
}

