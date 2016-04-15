<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 5:37
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections {

	use Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools\IEquivalentRecognizerAware;

	/** Positionable[0][1][3] linear collection
	 * Interface IStack
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 *
	 * @TODO Реализовать цепочку обязаностей IStack>IStack>IStack доступа к элементам коллекций (буфферизация, )
	 * @TODO Реализовать возможность буфферизации коллекций
	 * @TODO События коллекций
	 *
	 */
	interface IStack extends IStackRead, IStackWrite, IEquivalentRecognizerAware {}

}