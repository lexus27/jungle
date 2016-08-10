<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 6:02
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections {

	/**
	 * Interface IStackRead
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface IStackRead{

		/**
		 * @param $position
		 * @return mixed
		 */
		function getOf($position);

		/**
		 * @see getof
		 * @param $position
		 * @return bool
		 */
		function hasOf($position);



		/**
		 * @impl add,search,removeNode
		 * Важно: если стек не контролирует уникальность значений,
		 * то возвращаемая позиция будет актуальна для первого найденного идущего от верха к низу значения
		 * @param mixed $value
		 * @return false|int $position
		 */
		function search($value);

		/**
		 * @see search
		 * @param $value
		 * @return bool
		 */
		function exists($value);

		/**
		 * @param $value
		 * @return int
		 */
		function countValues($value);



		/**
		 * Получить срез стека по порядковым номерам
		 * @see array_slice
		 * @param int $start Поддерживается отрицательное значение
		 * @param int|null $length если равен NULL то будет дочитан стек до конечного элемента
		 * @param null $preserve_keys
		 * @return IStack
		 */
		function slice($start, $length = null,$preserve_keys = null);


		/**
		 * @see array_chunk
		 * @param array $input
		 * @param $size
		 * @param null $preserve_keys
		 * @return IStack[] стеки частями (Текущий стек не уничтожится)
		 */
		function chunk(array $input, $size, $preserve_keys = null);

	}
}

