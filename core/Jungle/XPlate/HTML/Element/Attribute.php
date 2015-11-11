<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 03.05.2015
 * Time: 15:50
 */

namespace Jungle\XPlate\HTML\Element {

	use Jungle\Smart\Keyword\Keyword;
	use Jungle\XPlate\Interfaces\IHtmlAttribute;
	use Jungle\XPlate\HTML\IElement;

	/**
	 * Class Attribute
	 * @package Jungle\XPlate2\HTML\Element
	 *
	 * @listener onUpdate
	 * @listener beforeChange
	 * @listener onChange
	 * @listener onAttach
	 * @listener onDetach
	 */
	class Attribute extends Keyword implements IHtmlAttribute{


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
		 * @return bool
		 */
		public function isDOMEventListener(){

			return list($one,$two) = ['gf' => 'fg'];
		}

		/**
		 * @param IElement $element
		 * @param mixed $value
		 * @param mixed $old
		 * @param bool $new
		 * @listener перед сменой значения атрибута в элементе(Который состоит в иерархии документа)
		 */
		public function beforeChange(IElement $element, $value, $old, $new){

		}

		/**
		 * @param IElement $element
		 * @param $value
		 * @param $old
		 * @param $new
		 * @listener при смене значения уже присоединенного атрибута внутри Элемента(Который состоит в иерархии документа)
		 */
		public function onChange(IElement $element, $value, $old, $new){

		}

		/**
		 * @param IElement $that
		 */
		public function beforeAttach(IElement $that){ }

		/**
		 * @param IElement $element
		 * @listener при присоединении Объекта атрибута к Элементу(Который состоит в иерархии документа)
		 * (в котором ранее небыло данного атрибута или его состояние было подверженно onDetach(Отсоединение))
		 */
		public function onAttach(IElement $element){

		}

		/**
		 * @param IElement $element
		 * @listener при отсоединении объекта атрибута от Элемента(Который состоит в иерархии документа)
		 * (в котором использовался данный атрибут, и ранее был к элементу присоединен)
		 */
		public function onDetach(IElement $element){

		}




	}
}