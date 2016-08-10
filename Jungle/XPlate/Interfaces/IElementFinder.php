<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.09.2015
 * Time: 20:33
 */
namespace Jungle\XPlate\Interfaces {


	/**
	 * Interface IElementFinder
	 * @package Jungle\XPlate\Interfaces
	 */
	interface IElementFinder{

		public function querySelector($cssSelector);

		public function querySelectorAll($cssSelector);

		public function getElementById($id);

		public function getElementsByTagName($tagName);

		public function getElementsByClassName($className);

		public function getElementsByAttributeExists($attributeKey);

		public function getElementsByAttributeValue($attributeKey, $value);

		public function getElementsBy(callable $callable);


	}
}

