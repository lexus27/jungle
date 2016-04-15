<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 5:48
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\Concretes {

	/**
	 * Aware - Знающий
	 * Option Aware Object
	 * Interface IOptionsAware
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 * Реализация знает где брать опции по ключу
	 */
	interface IOptionsAware extends IOptionsAwareRead, IOptionsAwareWrite{}

}

