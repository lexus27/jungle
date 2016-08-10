<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 0:18
 */
namespace Jungle\XPlate\CSS\Selector\Dependency {

	use Jungle\XPlate\CSS\Selector\Combination;
	use Jungle\XPlate\CSS\Selector\Dependency;
	use Jungle\XPlate\HTML\IElement;

	/**
	 * Class NextSibling
	 * @package Jungle\XPlate\CSS\Selector\Dependency
	 */
	class NextSibling extends Dependency{

		protected $symbol = '+';

		/**
		 * @param Combination $combination
		 * @param IElement $element
		 * @return IElement[]
		 */
		public function search(Combination $combination, IElement $element){
			$sibling = $element->nextSibling();
			if($sibling && $combination->check($sibling)){
				return [$sibling];
			}
			return [];
		}

	}
}

