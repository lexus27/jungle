<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 26.04.2015
 * Time: 17:51
 */

namespace Jungle\XPlate\HTML {

	use Jungle\XPlate\Interfaces\IElementFinder;
	use Jungle\XPlate\Interfaces\IWebStrategy;
	use Jungle\XPlate\Interfaces\IWebSubject;

	/**
	 * Class Document
	 * @package Jungle\XPlate2\HTML
	 */
	class Document implements IElementFinder, IWebSubject{

		use \Jungle\Util\ServiceContainerTrait;
		use \Jungle\Util\OptionContainerTrait;

		/**
		 * @var bool
		 */
		protected static $prettyMode = false;

		/**
		 * @var string
		 */
		protected static $prettyIndent = "\t";

		/**
		 * @var array
		 */
		protected static $preservedFormatTags = [
			'pre',
			'code',
			'script'
		];



		/**
		 * @var IWebStrategy
		 */
		protected $strategy;

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $doc_type;

		/**
		 * @var Element
		 */
		protected $html;

		/**
		 * @var Element
		 */
		protected $head;

		/**
		 * @var Element
		 */
		protected $body;

		/** Attached elements with property ownerDocument
		 * @var Element[]
		 */
		protected $elements = [];

		/**
		 * @param IWebStrategy $strategy
		 */
		public function __construct(IWebStrategy $strategy){
			$this->setStrategy($strategy);
			$this->_createRootElements();
		}

		/**
		 * Обновить Элементы документа
		 */
		public function update(){

		}

		/**
		 * @param IWebStrategy $strategy
		 * @param bool $appliedInStrategy
		 * @param bool $appliedInOld
		 * @return mixed
		 */
		public function setStrategy(IWebStrategy $strategy, $appliedInStrategy = false, $appliedInOld = false){
			$old = $this->strategy;
			if($old!==$strategy){
				if(!$strategy->isValid()){
					throw new \LogicException('Strategy is invalid!');
				}
				$this->strategy = $strategy;
				if($strategy && !$appliedInStrategy){
					$strategy->addSubject($this,true);
				}
				if($old && !$appliedInOld){
					$old->removeSubject($this,true);
				}
			}
			return $this;
		}

		/**
		 * @return IWebStrategy
		 */
		public function getStrategy(){
			return $this->strategy;
		}

		/**
		 * @param $id
		 * @return Element|null
		 */
		public function getElementById($id){
			foreach($this->elements as $element){
				if($element->isContainer() && $element->getAttribute('id') === $id){
					return $element;
				}
			}
			return null;
		}

		/**
		 * @param $name
		 * @return array
		 */
		public function getElementsByName($name){
			return $this->getElementsByAttributeValue('name',$name);
		}

		/**
		 * @param $className
		 */
		public function getElementsByClassName($className){
			// @TODO
		}

		/**
		 * @param $tagName
		 * @return array
		 */
		public function getElementsByTagName($tagName){
			$elements = [];
			$tagName = strtolower($tagName);
			foreach($this->elements as $element){
				if(!$element->isValue() && strtolower($element->getTag()) === $tagName){
					$elements[] = $element;
				}
			}
			return $elements;
		}

		/**
		 * @param $attribute
		 * @return array
		 */
		public function getElementsByAttributeExists($attribute){
			$elements = [];
			foreach($this->elements as $element){
				if($element->hasAttribute($attribute)){
					$elements[] = $element;
				}
			}
			return $elements;
		}

		/**
		 * @param $attribute
		 * @param $value
		 * @param callable|null $checker
		 * @return array
		 */
		public function getElementsByAttributeValue($attribute, $value,callable $checker = null){
			$elements = [];
			if(!$checker){
				$checker = function (& $v1, & $v2){
					return $v1 === $v2;
				};
			}
			foreach($this->elements as $element){
				if($element->hasAttribute($attribute) && call_user_func($checker,$element->getAttribute($attribute),$value)===true){
					$elements[] = $element;
				}
			}
			return $elements;
		}



		/** Добавление сопутствующего элемента
		 * @param Element $element
		 */
		public function addElement(Element $element){
			if($this->searchElement($element) === false){
				$this->elements[] = $element;
			}
		}

		/** Поиск сопуствующего элемента
		 * @param Element $element
		 * @return mixed
		 */
		public function searchElement(Element $element){
			return array_search($element, $this->elements, true);
		}

		/** Удаление сопутствующего элемента
		 * @param Element $element
		 */
		public function removeElement(Element $element){
			if(($i = $this->searchElement($element)) !== false){
				array_splice($this->elements, $i, 1);
			}
		}


		/**
		 * Создает корневые элементы документа если их нету
		 */
		protected function _createRootElements(){
			if(!$this->html){
				$this->html = new Element();
				$this->html->setTag('html');
				$this->html->setOwnerDocument($this);
			}
			if(!$this->head){
				$this->head = new Element();
				$this->head->setTag('head');
				$this->html->append($this->head);
			}
			if(!$this->body){
				$this->body = new Element();
				$this->body->setTag('body');
				$this->html->append($this->body);
			}
		}

		/** Корневой элемент HTML
		 * @return Element
		 */
		public function getHtml(){
			return $this->html;
		}

		/** Корневой элемент HEAD
		 * @return Element
		 */
		public function getHead(){
			return $this->head;
		}

		/** Корневой элемент BODY
		 * @return Element
		 */
		public function getBody(){
			return $this->body;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			$carriage = self::isPrettyMode() ? "\r\n" : '';
			$s = [
				'<!DOCTYPE html>', $this->getHtml()->getOuterHTML(),
			];
			return implode($carriage, $s);
		}




		/**
		 * @param $tag
		 * @return bool
		 */
		public static function isChildrenFormatPreserved($tag){
			return in_array(strtolower($tag), self::$preservedFormatTags, true);
		}

		/**
		 * @param array $tagNames
		 */
		public static function setPreservedFormatContainers(array $tagNames = ['pre', 'code', 'script']){
			self::$preservedFormatTags = array_map(
				function ($v){
					return strtolower($v);
				}, $tagNames
			);
		}

		/**
		 * @param bool $isPretty
		 */
		public static function setPrettyMode($isPretty = true){
			self::$prettyMode = boolval($isPretty);
		}

		/**
		 * @return bool
		 */
		public static function isPrettyMode(){
			return self::$prettyMode;
		}

		/**
		 * @param string $indent
		 */
		public static function setPrettyIndent($indent = "\t"){
			self::$prettyIndent = $indent;
		}

		/**
		 * @return string
		 */
		public static function getPrettyIndent(){
			return self::$prettyIndent;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if($this->name !== $name){
				$this->name = $name;
			}
			return $this;
		}

		public function getElementsBy(callable $callback){
			$elements = [];
			foreach($this->elements as $element){
				if(call_user_func($callback,$element)===true){
					$elements[] = $element;
				}
			}
			return $elements;
		}

		public function querySelector($cssSelector){
			// TODO: Implement querySelector() method.
		}

		public function querySelectorAll($cssSelector){
			// TODO: Implement querySelectorAll() method.
		}

		public function refreshStrategy(){
			// TODO: Implement refreshStrategy() method.
		}
	}
}