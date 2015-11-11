<?php

namespace Jungle\XPlate\CSS\Selector {

	use Jungle\XPlate\CSS\Selector\Marker\Cls;
	use Jungle\XPlate\HTML\IElement;
	use Jungle\XPlate\Interfaces\IHtmlTag;

	/**
	 * Class Combination
	 * @package Jungle\XPlate\CSS\Selector
	 * Комбинация используемых Модификаторов и Аттрибут-запросов с Субьектом
	 *
	 * <if tag><subject><modifiers>
	 * combination = div#id.class1.class2:hover:visited
	 */
	class Combination{

		/**
		 * @var IHtmlTag|null
		 */
		protected $tag;

		/**
		 * @var Subject|null
		 */
		protected $subject;

		/**
		 * @var ModifierQuery[]
		 */
		protected $modifierQueries = [];

		/**
		 * @var AttributeQuery[]
		 */
		protected $attributeQueries = [];


		/**
		 * @param IHtmlTag|null $tag
		 * @return $this
		 */
		public function setTag(IHtmlTag $tag = null){
			$this->tag = $tag;
			return $this;
		}

		/**
		 * @return IHtmlTag|null
		 */
		public function getTag(){
			return $this->tag;
		}



		/**
		 * @param Subject $subject
		 * @return $this
		 */
		public function setSubject(Subject $subject){
			$this->subject = $subject;
			return $this;
		}

		/**
		 * @return Subject|null
		 */
		public function getSubject(){
			return $this->subject;
		}


		/**
		 * @param ModifierQuery $modifier
		 * @return $this
		 */
		public function addModifierQuery(ModifierQuery $modifier){
			$i = $this->searchModifierQuery($modifier);
			if($i === false){
				$this->modifierQueries[] = $modifier;
			}
			return $this;
		}

		/**
		 * @param ModifierQuery $modifier
		 * @return mixed
		 */
		public function searchModifierQuery(ModifierQuery $modifier){
			return array_search($modifier, $this->modifierQueries, true);
		}

		/**
		 * @param ModifierQuery $modifier
		 * @return $this
		 */
		public function removeModifierQuery(ModifierQuery $modifier){
			$i = $this->searchModifierQuery($modifier);
			if($i !== false){
				array_splice($this->modifierQueries, $i, 1);
			}
			return $this;
		}

		/**
		 * @return ModifierQuery[]
		 */
		public function getModifierQueries(){
			return $this->modifierQueries;
		}


		/**
		 * @param AttributeQuery $aq
		 * @return $this
		 */
		public function addAttributeQuery(AttributeQuery $aq){
			$i = $this->searchAttributeQuery($aq);
			if($i === false){
				$this->attributeQueries[] = $aq;
			}
			return $this;
		}

		/**
		 * @param AttributeQuery $aq
		 * @return mixed
		 */
		public function searchAttributeQuery(AttributeQuery $aq){
			return array_search($aq,$this->attributeQueries,true);
		}

		/**
		 * @param AttributeQuery $aq
		 * @return $this
		 */
		public function removeAttributeQuery(AttributeQuery $aq){
			$i = $this->searchAttributeQuery($aq);
			if($i !== false){
				array_splice($this->attributeQueries,$i,1);
			}
			return $this;
		}

		/**
		 * @return AttributeQuery[]
		 */
		public function getAttributeQueries(){
			return $this->attributeQueries;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return
				$this->getTag() .
				$this->getSubject() .
				implode('',$this->attributeQueries) .
				implode('',$this->modifierQueries)
			;
		}

		/**
		 * @param IElement $element
		 * @return bool
		 */
		public function __invoke(IElement $element){
			return $this->check($element);
		}

		/**
		 * @param IElement $element
		 * @return bool
		 */
		public function check(IElement $element){
			if($this->subject){

				$id = $this->subject->getIdentifier();
				if($id && $element->getAttribute('id')!==$id){
					return false;
				}

				$classes = $this->subject->getClasses();
				if($classes && is_array($classes)){
					/**
					 * @var Cls[] $attributeQueries
					 */
					foreach($classes as $cls){
						if(!$element->hasClass($cls)){
							return false;
						}
					}
				}

			}

			if($this->attributeQueries){
				/**
				 * @var AttributeQuery[] $attributeQueries
				 */
				foreach($this->attributeQueries as $aQuery){
					if(!$aQuery->check($element->getAttribute($aQuery->getAttribute()))){
						return false;
					}
				}
			}

			if($this->modifierQueries){

			}
			return true;
		}


	}
}