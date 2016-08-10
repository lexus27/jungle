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

	class All extends Dependency{

		protected $symbol = ' ';

		/**
		 * @param Combination $combination
		 * @param IElement $element
		 * @return mixed
		 */
		public function search(Combination $combination, IElement $element){
			return $element->getElementsBy($combination);
		}


	}
}

