<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.03.2016
 * Time: 14:00
 */
namespace Jungle\Data\Collection {
	/**
	 * Interface EquivalentRecognizerAwareInterface
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 *
	 * Реализация знает зачем ей определитель эквивалента значения
	 *      Одна из важных особенностей коллекций
	 */
	interface EquivalentRecognizerAwareInterface{

		/**
		 * Опознаватель значения при его поиске в стеке по методу @see IStackRead::search
		 * @param callable|null $recognizer
		 * function($passedToSearchValue, $internalEachValueCheck){
		 *      return $passedToSearchValue === $$internalEachValueCheck;
		 * }
		 * @return $this
		 */
		function setEquivalentRecognizer(callable $recognizer = null);

		/**
		 * @return callable|null
		 */
		function getEquivalentRecognizer();

		/**
		 * @param mixed $baseValue
		 * @param mixed $compareValue
		 * @return bool will be recognized
		 */
		function recognizeEqual($baseValue, $compareValue);

	}
}

