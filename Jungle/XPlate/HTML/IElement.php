<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 26.04.2015
 * Time: 18:02
 */

namespace Jungle\XPlate\HTML {

	use Jungle\XPlate\Interfaces\IElementFinder;

	/**
	 * Interface IElement
	 * @package Jungle\XPlate2\HTML
	 */
	interface IElement extends IElementFinder{

		/**
		 * @return bool
		 */
		public function checkDummy();

		/**
		 * @param $tag
		 * @return $this
		 */
		public function setTag($tag);

		/**
		 * @return mixed
		 */
		public function getTag();

		/**
		 * @param IElement|null $element
		 * @param bool|false $added
		 * @param bool $removedFromOld
		 * @return $this
		 */
		public function setParent(IElement $element = null, $added = false,$removedFromOld = false);

		/**
		 * @return mixed
		 */
		public function getParent();

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setAttribute($key, $value);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getAttribute($key);

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasAttribute($key);

		/**
		 * @param $key
		 * @return $this
		 */
		public function unsetAttribute($key);

		/**
		 * @param $offset
		 * @param $count
		 * @param array|null $replacement
		 * @param bool $appliedInParent
		 * @param bool $appliedInOld
		 * @return mixed
		 */
		public function splice($offset, $count, array $replacement = null, $appliedInParent = false, $appliedInOld = false);

		/**
		 * @param $element
		 * @return mixed
		 */
		public function append($element);

		/**
		 * @param $element
		 * @return mixed
		 */
		public function prepend($element);

		/**
		 * @param $element
		 * @param IElement $before
		 * @return mixed
		 */
		public function insertBefore($element, IElement $before);

		/**
		 * @param $element
		 * @param IElement $after
		 * @return mixed
		 */
		public function insertAfter($element, IElement $after);



		/**
		 * @param $element
		 * @return $this
		 * @TODO implement in inheritors
		 */
		public function insertBehind($element);

		/**
		 * @param $element
		 * @return $this
		 * @TODO implement in inheritors
		 */
		public function insertAhead($element);




		/**
		 * @param IElement $element
		 * @return mixed
		 */
		public function indexOf(IElement $element);

		/**
		 * @param $subject
		 * @param IElement $source
		 * @return mixed
		 */
		public function replace($subject, IElement $source);

		/**
		 * @param IElement $element
		 * @return mixed
		 */
		public function remove(IElement $element);

		/**
		 * @param IElement $el
		 * @param int|bool $depth
		 * @return bool
		 */
		public function contains(IElement $el, $depth = true);

		/**
		 * @param $value
		 * @return mixed
		 */
		public function setValue($value);

		/**
		 * @return mixed
		 */
		public function getValue();

		/**
		 * @param bool|true $ws
		 * @return mixed
		 */
		public function setBeforeWhitespace($ws = true);

		/**
		 * @param bool|true $ws
		 * @return mixed
		 */
		public function setAfterWhitespace($ws = true);

		/**
		 * @return bool
		 */
		public function hasBeforeWhitespace();

		/**
		 * @return bool
		 */
		public function hasAfterWhitespace();

		/**
		 * @return bool
		 */
		public function isValue();

		/**
		 * @return bool
		 */
		public function isContainer();

		/**
		 * @param bool|false $pretty
		 * @param int $levelDepth
		 * @return string
		 */
		public function getOuterHTML($pretty = false, $levelDepth = 0);

		/**
		 * @param bool|false $pretty
		 * @param int $levelDepth
		 * @return string
		 */
		public function getInnerHTML($pretty = false, $levelDepth = 0);

		/**
		 * @return string
		 */
		public function __toString();

		/**
		 * @return IElement
		 */
		public function lastPlain();

		/**
		 * @return IElement
		 */
		public function firstPlain();

		/**
		 * @return IElement
		 */
		public function nextPlain();

		/**
		 * @return IElement
		 */
		public function prevPlain();

		/**
		 * @param $index
		 *
		 * @return IElement
		 */
		public function getChild($index);

		/**
		 * @return IElement[]
		 */
		public function getChildren();

		/**
		 * @return Document
		 */
		public function getOwnerDocument();

		/**
		 * @return IElement|null
		 */
		public function nextSibling();

		/**
		 * @return IElement|null
		 */
		public function prevSibling();



	}
}