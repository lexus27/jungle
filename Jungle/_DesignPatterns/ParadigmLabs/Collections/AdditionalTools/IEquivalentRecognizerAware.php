<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 6:29
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {

	/**
	 * Interface IEquivalentRecognizerAware
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 *
	 * Реализация знает зачем ей определитель эквивалента значения
	 *      Одна из важных особенностей коллекций
	 */
	interface IEquivalentRecognizerAware{

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

