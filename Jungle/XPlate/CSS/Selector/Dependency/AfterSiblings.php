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

	class AfterSiblings extends Dependency{


		protected $symbol = '~';

		/**
		 * @param Combination $combination
		 * @param IElement $element
		 * @return IElement[]
		 */
		public function search(Combination $combination, IElement $element){
			$elements = [];
			$sibling = $element->nextSibling();
			while($sibling){
				if($combination->check($sibling)){
					$elements[] = $sibling;
				}
				$sibling = $element->nextSibling();
			}
			return $elements;
		}

	}
}

