<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 6:02
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections {

	/**
	 * Interface IStackWrite
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 *
	 * Здесь просто сохраняется порядок расположения элементов,
	 * выборки по индексу как таковой в IStack не предусмотрено,
	 * такой объект хорошо использовать при итерациях где важен момент и порядок появления определенных элементов
	 */
	interface IStackWrite{

		/**
		 * @impl add,search,removeNode collection
		 * @see append
		 * @param $value
		 * @return $this
		 */
		function add($value);

		/**
		 * @impl add,search,removeNode collection
		 * Удаляет значение используя как минимум: @see IStackRead::search
		 * Если стек не контролирует уникальность то {removeNode} проявляет свою
		 * универсальность и пошагово удаляет каждый совпавший с {search} позишен
		 * @param $value
		 * @return $this
		 */
		function remove($value);


		/**
		 * @param $value
		 * @return $this
		 */
		function append($value);

		/**
		 * @param $value
		 * @return $this
		 */
		function prepend($value);

		/**
		 * @param int $position  existed index or index long than last index
		 * @param mixed $value
		 * @param bool $behind (not override)
		 * @return $this
		 */
		function insert($position, $value, $behind = true);

		/**
		 * @param $position
		 * @param $count
		 * @param array|null|IStack $replacement
		 * @param bool $returnStack
		 * @return array|IStack|null
		 * @internal param bool $onlyStacks $replacement and return strict to IStack object
		 */
		function splice($position, $count, $replacement = null,$returnStack = false);

	}
}

