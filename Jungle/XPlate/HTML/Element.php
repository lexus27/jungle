<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 26.04.2015
 * Time: 17:52
 */

namespace Jungle\XPlate\HTML {

	use Jungle\Util\ObjectStorage;
	use Jungle\XPlate\HTML\Element\Attribute;
	use Jungle\XPlate\HTML\Element\Tag;
	use Jungle\XPlate\WebStrategy;

	/**
	 * Class Element
	 * @package Jungle\XPlate2\HTML
	 */
	class Element implements IElement{

		use \Jungle\Util\ObservableTrait;

		const WS_BEFORE = 1;
		const WS_AFTER = 2;

		/**
		 * @var Document
		 */
		protected $ownerDocument;

		/**
		 * @var Element
		 */
		protected $parent;

		/**
		 * @var Tag
		 */
		protected $tag;

		/**
		 * @var bool
		 */
		protected $closed = false;

		/**
		 * @var bool
		 */
		protected $closedIsSet = false;

		/**
		 * @var ObjectStorage
		 */
		protected $attributes = [];

		/**
		 * @var Element[]
		 */
		protected $children = [];

		/**
		 * @var string|object
		 */
		protected $value;

		/**
		 * @var int bit-mask
		 */
		protected $value_ws;


		public function __construct(){

			$this->addEvent([

				'beforeTagAttach',
				'onTagAttach',
				'onTagDetach',

				'beforeAttributeAttach',
				'onAttributeAttach',
				'onAttributeDetach',

				'beforeAttributeChange',
				'onAttributeChange',



				'onParentChange',
				'onParentAttach',
				'onParentDetach',

				'onChildAdd',
				'beforeChildAdd',
				'onChildRemove',

				'beforeRender',
				'beforeStartChildrenRender',
				'beforeChildRender',
				'beforeAttributeRender',
			]);

		}

		/**
		 * @return \Jungle\XPlate\Interfaces\IWebStrategy
		 */
		protected function getWebStrategy(){
			$strategy = WebStrategy::getDefault();
			if(!$strategy->isLocked()){
				throw new \LogicException('Prepare WebStrategy before use IElement`s');
			}
			return $strategy;
		}

		/**
		 * @param $tag
		 * @return $this
		 */
		public function setTag($tag){
			$tag = $this->getWebStrategy()->getTagManager()->get($tag);
			$old = $this->tag;
			if(
				$old !== $tag &&
				(!$tag || ($tag && $tag->beforeAttach($this)!==false && $this->invokeEvent('beforeTagAttach',$this,$tag)!==false))
			){
				$this->tag = $tag;
				if($old){
					$old->onDetach($this);
					$this->invokeEvent('onTagDetach',$this,$old);
				}
				if($tag){
					$tag->onAttach($this);
					$this->invokeEvent('onTagAttach',$this,$tag);
				}
			}
			return $this;
		}

		/**
		 * @return Tag
		 */
		public function getTag(){
			return $this->tag;
		}

		/**
		 * @param $closed
		 */
		public function setClosed($closed){
			if($this->closed !== $closed){
				$this->closedIsSet = true;
				$this->closed = $closed;
			}
		}

		/**
		 * @return bool
		 */
		public function isClosed(){
			if($this->tag){
				$closed = $this->tag->isClosed();
				if($this->tag->isClosedStrict()){
					return $closed!==null?$closed:$this->closed;
				}else{
					return $this->closedIsSet?$this->closed:$closed;
				}
			}else{
				return $this->closed;
			}
		}

		/**
		 * @return bool
		 */
		public function isContainer(){
			return $this->getTag() && !$this->isClosed();
		}

		/**
		 * @return bool
		 */
		public function isValue(){
			return $this->getTag() ? false : true;
		}

		/**
		 * @return bool
		 */
		public function isEmpty(){
			if(!$this->isValue()){
				return $this->isContainer() && !$this->children && !$this->countAttributes();
			}else{
				return !$this->value;
			}
		}


		/**
		 * @param Attribute|string $attribute
		 * @param mixed $value
		 * @return $this
		 */
		public function setAttribute($attribute, $value){
			if(!$this->isContainer()) throw new \LogicException('This element is not a container , setTag before Attributes manipulation');
			if(!$this->attributes instanceof ObjectStorage) $this->attributes = new ObjectStorage();
			$attribute = $this->getWebStrategy()->getAttributeManager()->get($attribute);
			$old = $this->getAttribute($attribute);
			$new = !$this->hasAttribute($attribute);
			if(
				(($new && $attribute->beforeAttach($this) !== false &&  $this->invokeEvent('beforeAttributeAttach',$this,$attribute) !== false) || !$new) &&
				($attribute->beforeChange($this,$value, $old, $new) !== false) &&
				($this->invokeEvent('beforeAttributeChange',$this,$value, $old, $new) !== false)
			){
				$this->attributes->set($attribute,$value);
				if($new){
					$attribute->onAttach($this);
					$this->invokeEvent('onAttributeAttach',$this,$attribute);
				}
				$attribute->onChange($this,$value,$old,$new);
				$this->invokeEvent('onAttributeChange',$this,$value,$old,$new);
			}
			return $this;
		}

		/**
		 * @param Attribute|string $attribute
		 * @return mixed
		 */
		public function getAttribute($attribute){
			if(!$this->attributes instanceof ObjectStorage)return null;
			$attribute = $this->getWebStrategy()->getAttributeManager()->get($attribute);
			return $this->attributes->get($attribute);
		}

		/**
		 * @param Attribute|string $attribute
		 * @return bool
		 */
		public function hasAttribute($attribute){
			if(!$this->attributes instanceof ObjectStorage)return false;
			$attribute = $this->getWebStrategy()->getAttributeManager()->get($attribute);
			return $this->attributes->has($attribute);
		}

		/**
		 * @param Attribute|string $attribute
		 * @return $this
		 */
		public function unsetAttribute($attribute){
			if(!$this->attributes instanceof ObjectStorage)return $this;
			$attribute = $this->getWebStrategy()->getAttributeManager()->get($attribute);
			if($this->hasAttribute($attribute)){
				$this->attributes->remove($attribute);
				$attribute->onDetach($this);
				$this->invokeEvent('onAttributeDetach',$this,$attribute);
			}
			return $this;
		}

		/**
		 * @return int
		 */
		public function countAttributes(){
			return $this->attributes instanceof ObjectStorage?$this->attributes->count():0;
		}

		/**
		 * @return array
		 */
		public function getAttributesArray(){
			$a = [];
			if($this->attributes instanceof ObjectStorage){
				foreach($this->attributes as $attr){
					$value = $this->attributes->get($attr);
					$a[$attr->getIdentifier()] = $value;
				}
			}
			return $a;
		}

		/**
		 * @return string
		 */
		protected function getAttributesString(){
			$a = [];
			foreach($this->getAttributesArray() as $key => $value){
				if($this->invokeEvent('beforeAttributeRender',$key,$value)!==false){
					$value = htmlentities($value);
					$a[] = "{$key}=\"{$value}\"";
				}
			}
			return implode(' ', $a);
		}


		/**
		 * Работа с вложеными элементами
		 */

		/**
		 * @param int $offset
		 * @param int $count
		 * @param array $replacement
		 * @param bool $appliedInParent
		 * @param bool $appliedInOld
		 * @return array
		 */
		public function splice($offset = null, $count = 0, array $replacement = null, $appliedInParent = false, $appliedInOld = false){
			/** @var IElement|self $that */
			$that = $this;
			if($replacement){
				$replacement = ElementFactory::getFactory()->decomposeRow($replacement);
				$replacement = array_filter(
					$replacement, function (IElement $v) use ($that){
					if($v->isContainer() && $v->contains($that, true)){
						return false;
						//passed element is ancestor this container!
					}
					if($that->contains($v)){
						return false;
						//passed element already contains in this container!
					}
					if($that->tag && $that->tag->beforeChildAdd($that,$v)===false){
						return false;
					}
					return true;
				}
				);
			}
			if($offset===null){
				$offset = count($this->children);
			}
			$spliced = array_splice($this->children, $offset, $count, $replacement);

			# detach spliced
			if($spliced && (!$appliedInOld||$this->tag)){
				/** @var IElement $el */
				foreach($spliced as $el){
					if(!$appliedInOld)$el->setParent(null,false,true);
					$this->invokeEvent('onChildRemove',$this,$el);
				}
			}

			# Set parent to added elements:
			if($replacement && (!$appliedInParent||$this->tag)){
				/** @var IElement $el */
				foreach($replacement as $el){
					if(!$appliedInParent)$el->setParent($that, true);
					$this->invokeEvent('onChildAdd',$this,$el);
				}
			}
			return $spliced;
		}

		/**
		 * @param IElement $element
		 * @return IElement
		 */
		public function remove(IElement $element){
			$i = $this->indexOf($element);
			if($i !== false){
				$this->splice($i, 1);
			}
			return $this;
		}

		/**
		 * @param $element
		 * @return $this
		 */
		public function append($element){
			$this->splice(count($this->children), 0, [$element]);
			return $this;
		}

		/**
		 * @param $element
		 * @return $this
		 */
		public function prepend($element){
			$this->splice(0, 0, [$element]);
			return $this;
		}

		/**
		 * @param $element
		 * @param IElement $before
		 * @return $this
		 */
		public function insertBefore($element, IElement $before){
			if(($i = $this->indexOf($before)) !== false){
				$this->splice($i, 0, [$element]);
			}
			return $this;
		}

		/**
		 * @param $element
		 * @param IElement $after
		 * @return $this
		 */
		public function insertAfter($element, IElement $after){
			if(($i = $this->indexOf($after)) !== false){
				$this->splice($i + 1, 0, [$element]);
			}
			return $this;
		}

		/**
		 * @param $element
		 * @return $this
		 */
		public function insertBehind($element){
			return $this->getParent()->insertBefore($element,$this);
		}

		/**
		 * @param $element
		 * @return $this
		 */
		public function insertAhead($element){



		}


		/**
		 * @param $subject
		 * @param IElement $srcEl
		 * @return array|null
		 */
		public function replace($subject, IElement $srcEl){
			if($subject instanceof IElement){
				$elI = $this->indexOf($subject);
			}else{
				$elI = false;
			}
			$srcI = $this->indexOf($srcEl);
			if($srcI !== false && $elI === false){
				$splice = $this->splice($srcI, 1, [$subject], true);
				return $splice;
			}elseif($srcI !== false && $elI !== false){
				$this->children[$srcI] = $subject;
				$this->children[$elI] = $srcEl;
			}else{
				throw new \InvalidArgumentException('Replacement not found');
			}
			return null;
		}

		/**
		 * @param IElement $element
		 * @return mixed
		 */
		public function indexOf(IElement $element){
			return array_search($element, $this->children, true);
		}

		/**
		 * @param $index
		 * @return Element|null
		 */
		public function getChild($index){
			return isset($this->children[$index]) ? $this->children[$index] : null;
		}

		/**
		 * @return Element[]
		 */
		public function getChildren(){
			return $this->children;
		}

		/**
		 * @param IElement $el
		 * @param bool|int $depth
		 * @return bool
		 */
		public function contains(IElement $el, $depth = true){
			if($depth < 2 && $depth !== true){
				return $this->indexOf($el) !== false;
			}else{
				foreach($this->children as & $o){
					if($o === $el){
						return true;
					}
					if($o->isContainer()){
						$m = $o->contains($el, ($depth === true ? $depth : $depth - 1));
						if($m === true){
							return $m;
						}
					}
				}
				return false;
			}
		}


		public function querySelector($cssSelector){

		}

		public function querySelectorAll($cssSelector){

		}

		/**
		 * @param $id
		 * @return Element|null
		 */
		public function getElementById($id){
			if($this->isContainer() && !$this->isClosed()){
				foreach($this->children as $child){
					if($child->hasAttribute('id') && $child->getAttribute('id') === $id){
						return $child;
					}elseif(($element = $child->getElementById($id))){
						return $element;
					}
				}
			}
			return null;
		}

		/**
		 * @param $tagName
		 * @return Element[]
		 */
		public function getElementsByTagName($tagName){
			$tagName = strtolower($tagName);
			return $this->getElementsBy(
				function (Element $child) use ($tagName){
					return !$child->isValue() && strtolower($child->getTag()) === $tagName;
				}
			);
		}

		public function getElementsByClassName($className, array & $elements = []){
			// @TODO
		}

		/**
		 * @param $attribute
		 * @return Element[]
		 */
		public function getElementsByAttributeExists($attribute){
			return $this->getElementsBy(
				function (Element $child) use ($attribute){
					return $child->hasAttribute($attribute);
				}
			);
		}

		/**
		 * @param callable $checker function(IElement $element, $value){return is_string($value);}
		 * @return Element[]
		 */
		public function getElementsByValue(callable $checker){
			return $this->getElementsBy(
				function (Element $child) use ($checker){
					return $child->isValue() && call_user_func($checker,$child,$child->getValue())===true;
				}
			);
		}

		/**
		 * @param $attribute
		 * @param $value
		 * @param callable $compareFunc
		 * @return Element[]
		 */
		public function getElementsByAttributeValue($attribute, $value, callable $compareFunc = null){
			if(!$compareFunc){
				$compareFunc = function (& $v1, & $v2){
					return $v1 === $v2;
				};
			}
			return $this->getElementsBy(
				function (Element $child) use ($attribute, $value, $compareFunc){
					return !$child->isValue() && $child->hasAttribute($attribute) && call_user_func(
						$compareFunc, $child->getAttribute($attribute), $value
					);
				}
			);
		}

		/**
		 * @param callable $callback
		 * @param array $elements
		 * @return Element[]
		 */
		public function getElementsBy(callable $callback, array & $elements = []){
			if($this->isContainer()){
				foreach($this->children as $child){
					if(call_user_func($callback, $child)){
						$elements[] = $child;
					}else{
						$child->getElementsBy($callback,$elements);
					}
				}
			}
			return $elements;
		}

		/**
		 * @return bool|mixed
		 */
		public function getContainIndex(){
			$parent = $this->getParent();
			return $parent ? $parent->indexOf($this) : false;
		}

		/**
		 * @param IElement $parent
		 * @param bool $added
		 * @param bool $removedFromOld
		 * @return $this
		 */
		public function setParent(IElement $parent = null, $added = false,$removedFromOld = false){
			$old = $this->parent;
			if($old !== $parent){
				$this->parent = $parent;
				$this->onParentChange($old);
				if($parent && !$added){
					$parent->splice(null,0,[$this],true);
				}
				if($old && !$removedFromOld){
					$old->splice($old->indexOf($this),1,null,false,true);
				}
				if($this->tag){
					if($parent) $this->invokeEvent('onParentAttach',$this,$parent);
					if($old) $this->invokeEvent('onParentDetach',$this,$old);
					$this->invokeEvent('onParentChange',$this,$parent,$old);
				}

			}
			return $this;
		}

		/**
		 * @param Element $oldParent
		 * @getCurrentListener onParentChange
		 */
		protected function onParentChange(Element $oldParent = null){
			$newParent = $this->getParent();
			if($newParent){
				$this->_setOwnerDocument($newParent->getOwnerDocument());
			}
		}

		/**
		 * @return Element
		 */
		public function getParent(){
			return $this->parent;
		}

		/**
		 * @param Document $document
		 */
		public function setOwnerDocument(Document $document){
			if(!$this->ownerDocument && !$this->parent){
				$this->_setOwnerDocument($document);
			}
		}

		/**
		 * @param Document $document
		 */
		protected function _setOwnerDocument(Document $document = null){
			$oldOwner = $this->ownerDocument;
			if($oldOwner !== $document){
				if(($parent = $this->getParent()) && ($pOwner = $parent->getOwnerDocument()) && ($pOwner !== $document)
				){
					throw new \LogicException(
						'У родителя текущего элемента есть присвоенный документ-владелец отличный от выставляемого,
					поэтому у его вложенных элементов не может быть другого документа-владельца,
					вообще не рекомендуется менять документ-владелец в ручную'
					);
				}

				$this->ownerDocument = $document;
				$document->addElement($this);
				$this->onOwnerDocumentChange($oldOwner);
			}
		}

		/**
		 * @param Document $oldOwner
		 * @getCurrentListener onOwnerDocumentChange
		 */
		protected function onOwnerDocumentChange(Document $oldOwner = null){
			if($oldOwner){
				$oldOwner->removeElement($this);
			}
			$newOwner = $this->getOwnerDocument();
			if($newOwner){

			}else{

			}
			/** Произвести соответствующие изменения у вложеных элементов */
			if($this->isContainer()){
				foreach($this->children as $child){
					$child->_setOwnerDocument($newOwner);
				}
			}
		}

		/**
		 * @return Document
		 */
		public function getOwnerDocument(){
			return $this->ownerDocument;
		}

		/**
		 * @param $id
		 * @return $this
		 */
		public function setId($id){
			$this->setAttribute('id',$id);
			return $this;
		}


		/**
		 * @param string|object $value
		 * @return $this
		 */
		public function setValue($value){
			$this->value = $value;
			return $this;
		}

		/**
		 * @return object|string
		 */
		public function getValue(){
			return $this->value;
		}


		/**
		 * @return Element|null
		 */
		public function first(){
			return $this->children ? $this->children[0] : null;
		}

		/**
		 * @return Element|null
		 */
		public function last(){
			return ($c = count($this->children)) ? $this->children[$c - 1] : null;
		}

		/**
		 * @return bool
		 */
		public function isFirst(){
			if(($p = $this->getParent())){
				return $p->first() === $this;
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function isLast(){
			if(($p = $this->getParent())){
				return $p->last() === $this;
			}
			return false;
		}

		/**
		 * @return Element|null
		 */
		public function firstPlain(){
			$plain = null;
			if($this->isContainer()){
				for($i = 0; $i < count($this->children); $i++){
					$child = $this->children[$i];
					if($child->isValue()){
						$plain = $child;
						break;
					}elseif(($plain = $child->firstPlain())){
						break;
					}
				}
			}
			return $plain;
		}

		/**
		 * @return Element|null
		 */
		public function lastPlain(){
			$plain = null;
			if($this->isContainer()){
				for($i = count($this->children) - 1; $i >= 0; $i--){
					$child = $this->children[$i];
					if($child->isValue()){
						$plain = $child;
						break;
					}else if(($plain = $child->lastPlain())){
						break;
					}
				}
			}
			return $plain;
		}

		/**
		 * @return Element|null
		 */
		public function nextSibling(){
			$i = $this->getContainIndex();
			if($i !== false){
				$parent = $this->getParent();
				return $parent->getChild($i + 1);
			}
			return null;
		}

		/**
		 * @return Element|null
		 */
		public function prevSibling(){
			$i = $this->getContainIndex();
			if($i !== false){
				$parent = $this->getParent();
				return $parent->getChild($i - 1);
			}
			return null;
		}

		/**
		 * @return Element|null
		 */
		public function nextPlain(){
			if(($s = $this->nextSibling()) instanceof IElement){
				if($s->isValue()){
					return $s;
				}else if($s->isContainer()){
					return $s->firstPlain();
				}else{
					return $s->nextPlain();
				}
			}elseif(($p = $this->getParent()) instanceof IElement){
				return $p->nextPlain();
			}
			return null;
		}

		/**
		 * @return Element|null
		 */
		public function prevPlain(){
			if(($s = $this->prevSibling()) instanceof IElement){
				if($s->isValue()){
					return $s;
				}else if($s->isContainer()){
					return $s->lastPlain();
				}else{
					return $s->prevPlain();
				}

			}elseif(($p = $this->getParent()) instanceof IElement){
				return $p->prevPlain();
			}
			return null;
		}

		/**
		 * @return bool
		 */
		public function hasBeforeWhitespace(){
			if($this->isValue()){
				return boolval($this->value_ws & self::WS_BEFORE);
			}else{
				$plain = $this->firstPlain();
				return $plain ? $plain->hasBeforeWhitespace() : false;
			}
		}

		/**
		 * @return bool
		 */
		public function hasAfterWhitespace(){
			if($this->isValue()){
				return boolval($this->value_ws & self::WS_AFTER);
			}else{
				$plain = $this->lastPlain();
				return $plain ? $plain->hasAfterWhitespace() : false;
			}
		}

		/**
		 * @param bool $has
		 * @return mixed|void
		 */
		public function setBeforeWhitespace($has = true){
			if($this->isValue()){
				if($has && !$this->hasBeforeWhitespace()){
					$this->value_ws = ($this->value_ws | self::WS_BEFORE);
				}else if(!$has){
					$this->value_ws = ($this->value_ws & ~self::WS_BEFORE);
				}
			}else if(($p = $this->firstPlain())){
				$p->setBeforeWhitespace($has);
			}
		}

		/**
		 * @param bool $has
		 * @return mixed|void
		 */
		public function setAfterWhitespace($has = true){
			if($this->isValue()){
				if($has && !$this->hasAfterWhitespace()){
					$this->value_ws = ($this->value_ws | self::WS_AFTER);
				}else if(!$has){
					$this->value_ws = ($this->value_ws & ~self::WS_AFTER);
				}
			}elseif(($p = $this->lastPlain())){
				$p->setAfterWhitespace($has);
			}
		}

		/**
		 * @return string
		 */
		protected function _getCtOpenHtml(){
			$attributesString = $this->getAttributesString();
			$attributesString = $attributesString ? ' ' . $attributesString : '';
			$isClosed = $this->isClosed();
			$closed = $isClosed ? '/' : '';
			return '<' . $this->tag . $attributesString . $closed . '>';
		}

		/**
		 * @return string
		 */
		protected function _getCtCloseHtml(){
			return '</' . $this->tag . '>';
		}

		/**
		 * @param bool $pretty
		 * @param bool $formatPreserved
		 * @param int $levelDepth
		 * @return string
		 */
		public function getOuterHTML($pretty = false, $formatPreserved = false, $levelDepth = 0){
			$html = '';
			if($this->isContainer() && $this->tag->beforeRender($this)!==false){
				$carriage = $pretty ? "\r\n" : '';
				$indentChar = $pretty ? Document::getPrettyIndent() : '';
				$baseIndent = $pretty ? str_repeat($indentChar, $levelDepth) : '';
				$html .= $baseIndent . $this->_getCtOpenHtml() . $carriage;
				if(!$this->isClosed()){
					if($this->tag->beforeStartChildrenRender($this)!==false){
						$html .= $this->getInnerHTML($pretty, $formatPreserved, $levelDepth + 1) . $carriage;
					}
					$html .= $baseIndent . $this->_getCtCloseHtml();
				}
			}elseif($this->isValue()){
				$html = $this->getInnerHTML($pretty, $formatPreserved, $levelDepth + 1);
			}
			return $html;
		}

		/**
		 * @param bool $pretty
		 * @param bool $formatPreserved
		 * @param int $levelDepth
		 * @return string
		 */
		public function getInnerHTML($pretty = false, $formatPreserved = false, $levelDepth = 0){
			if($this->isContainer() && !$this->isClosed()){
				$carriage = $pretty ? "\r\n" : '';
				$html = [];
				foreach($this->children as $child){
					if($this->tag->beforeChildRender($this,$child)!==false){
						$html[] = $child->getOuterHTML(
							$pretty,
							!$formatPreserved ? Document::isChildrenFormatPreserved($this->getTag()) : $formatPreserved,
							$pretty && $levelDepth > 0 ? $levelDepth + 1 : $levelDepth
						);
					}
				}
				return implode($carriage, $html);
			}elseif($this->isValue()){
				if($formatPreserved){
					return $this->value;
				}else{
					return ($this->hasBeforeWhitespace() ? ' ' : '') . trim(
						self::minimizeSpaces($this->value), "\r\n\t "
					) . ($this->hasAfterWhitespace() ? ' ' : '');
				}
			}else{
				return '';
			}
		}


		public function __toString(){
			return $this->getOuterHTML(Document::isPrettyMode());
		}


		public function checkDummy(){

		}

		/**
		 * @param $plainText
		 * @return string
		 */
		public static function minimizeSpaces($plainText){
			return preg_replace('@[\s\t\r\n]+@', ' ', $plainText);
		}
	}
}