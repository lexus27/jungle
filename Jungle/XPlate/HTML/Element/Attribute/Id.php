<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.09.2015
 * Time: 23:25
 */
namespace Jungle\XPlate\HTML\Element\Attribute {

	use Jungle\XPlate\HTML\Element\Attribute;
	use Jungle\XPlate\HTML\IElement;

	/**
	 * Class Id
	 * @package Jungle\XPlate\HTML\Element\Attribute
	 */
	class Id extends Attribute{

		/**
		 * @param IElement $element
		 * @param mixed $value
		 * @param mixed $old
		 * @param bool $new
		 * @return bool
		 */
		public function beforeChange(IElement $element, $value, $old, $new){
			$document = $element->getOwnerDocument();
			if($document->getElementById($value)){
				$name = $document->getName();
				throw new \LogicException('element with ID "'.$value.'" already exists in document ['.($name?$name:'No name').']');
			}
			return true;
		}


	}
}

