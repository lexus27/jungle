<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 03.05.2015
 * Time: 15:51
 */

namespace Jungle\XPlate\HTML\Element {

	use Jungle\Smart\Keyword\Keyword;
	use Jungle\XPlate\HTML\Element;
	use Jungle\XPlate\Interfaces\IHtmlTag;
	use Jungle\XPlate\HTML\IElement;

	/**
	 * Class Tag
	 * @package Jungle\XPlate2\HTML\Element
	 *
	 * @listener onUpdate
	 * @listener beforeAttach
	 * @listener onAttach
	 * @listener onDetach
	 */
	class Tag extends Keyword implements IHtmlTag{


		/**
		 * @return string
		 */
		public function getName(){
			return $this->getIdentifier();
		}

		/**
		 * @param $name
		 */
		public function setName($name){
			$this->setIdentifier($name);
		}

		/**
		 * @param IElement $element
		 * @Event
		 * Вызывается перед выставлением Tag IElement`у
		 */
		public function beforeAttach(IElement $element){




		}


		/**
		 * @param Element|IElement $element
		 * @Event
		 * Вызывается при присоединении тега к элементу
		 */
		public function onAttach(Element $element){
			$element->addListener($this);
		}

		/**
		 * @param Element|IElement $element
		 * @Event
		 * Вызывается при отсоединении тега от элемента
		 */
		public function onDetach(Element $element){
			$element->removeListener($this);
		}

		/**
		 * @param IElement $that
		 * @param IElement|null $parent
		 * @param IElement|null $old
		 */
		public function onParentChange(IElement $that,IElement $parent=null, IElement $old=null){}

		/**
		 * @param IElement $that - был вложен в $newParent
		 * @param IElement $newParent - Стал контейнером для $that
		 * Вызывается при выставлении родительского элемента
		 */
		public function onParentAttach(IElement $that,IElement $newParent){}

		/**
		 * @param IElement $that - был удален из $oldParent
		 * @param IElement $oldParent - ранее был контейнером для $that
		 * Вызывается после отсоединения родительского элемента
		 */
		public function onParentDetach(IElement $that, IElement $oldParent){}



		/**
		 * @param IElement $that
		 * @param IElement $child
		 * Вызывается как проверка перед тем как добавить дочерний элемент
		 */
		public function beforeChildAdd(IElement $that,IElement $child){}

		/**
		 * @param IElement $that
		 * @param IElement $child
		 * Вызывается при добавлении дочернего элемента
		 */
		public function onChildAdd(IElement $that,IElement $child){}


		/**
		 * @param IElement $that
		 * @param IElement $child
		 * Вызывается при удалении дочернего элемента
		 */
		public function onChildRemove(IElement $that,IElement $child){}

		/**
		 * @param IElement $element
		 * Вызывается перед тем как начать рендеринг
		 */
		public function beforeRender(IElement $element){}

		/**
		 * @param IElement $element
		 *  Вызывается как проверка перед тем как начать рендеринг дочерних элементов
		 */
		public function beforeStartChildrenRender(IElement $element){}

		/**
		 * @param IElement $element
		 * @param IElement $child
		 * Вызывается как проверка перед рендерингом определенного дочернего элемента
		 */
		public function beforeChildRender(IElement $element, IElement $child){}


		/**
		 * @param IElement $that
		 * @param Attribute $attribute
		 */
		public function beforeAttributeAttach(IElement $that, Attribute $attribute){}

		/**
		 * @param IElement $that
		 * @param Attribute $attribute
		 */
		public function onAttributeAttach(IElement $that, Attribute $attribute){}

		/**
		 * @param IElement $that
		 * @param Attribute $attribute
		 */
		public function onAttributeDetach(IElement $that, Attribute $attribute){}

		public function beforeAttributeChange(IElement $that, Attribute $attribute,$value,$old,$new){}

		public function onAttributeChange(IElement $that, Attribute $attribute,$value,$old,$new){}


		/**
		 * @return array
		 */
		public function getAllowedAttributeNames(){
			// TODO: Implement getAllowedAttributeNames() method.
		}

		/**
		 * @param string $attributeName
		 * @return $this
		 */
		public function addAllowedAttributeName($attributeName){
			// TODO: Implement addAllowedAttributeName() method.
		}

		/**
		 * @param string $attributeName
		 * @return bool|int
		 */
		public function searchAllowedAttributeName($attributeName){
			// TODO: Implement searchAllowedAttributeName() method.
		}

		/**
		 * @param string $attributeName
		 * @return $this
		 */
		public function removeAllowedAttributeName($attributeName){
			// TODO: Implement removeAllowedAttributeName() method.
		}

		/**
		 * @param string $attributeName
		 * @return bool
		 */
		public function isAllowedAttributeName($attributeName){
			// TODO: Implement isAllowedAttributeName() method.
		}

		/**
		 * @return bool
		 */
		public function isClosed(){
			return $this->getOption('closed',null);
		}

		/**
		 * @return bool
		 */
		public function isClosedStrict(){
			return $this->getOption('closed_strict',false);
		}
	}
}