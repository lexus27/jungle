<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 0:17
 */
namespace Jungle\XPlate\CSS\Selector\Dependency {

	use Jungle\XPlate\CSS\Selector\Combination;
	use Jungle\XPlate\CSS\Selector\Dependency;
	use Jungle\XPlate\HTML\IElement;

	class DirectChild extends Dependency{

		protected $symbol = '>';

		/**
		 * @param Combination $combination
		 * @param IElement $element
		 * @return IElement[]
		 */
		public function search(Combination $combination, IElement $element){
			$elements = [];
			$children = $element->getChildren();
			foreach($children as $e){
				if($e && $combination->check($e)){
					$elements[] = $e;
				}
			}
			return $elements;
		}

	}
}

