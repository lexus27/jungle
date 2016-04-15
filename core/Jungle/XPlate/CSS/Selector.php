<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 2:28
 */

namespace Jungle\XPlate\CSS {

	use Jungle\XPlate\CSS\Selector\AttributeQuery;
	use Jungle\XPlate\CSS\Selector\Combination;
	use Jungle\XPlate\CSS\Selector\Dependency;
	use Jungle\XPlate\HTML\Document as HTMLDocument;
	use Jungle\XPlate\HTML\IElement;

	/**
	 * Class Selector
	 * @package Jungle\XPlate\CSS
	 *
	 * Главный класс селектора
	 *
	 * Selector = div#id.Class:modifier[attributeQueryAttribute aqChecker= aqCollated] dependency div#id.Class:modifier[attributeQuery]
	 *
	 * Selector ->
	 *    	Combination ->
	 * 			- Tag
	 * 			- AttributeQuery
	 * 				- Attribute
	 * 				- Checker TypeChecker (AttributeName~=collatedValue != $= ^=)
	 * 				- Collated Value
	 * 			- Subject
	 * 				- Id(Marker)
	 * 				- Classes(Marker)
	 * 				- ModifierWrappers
	 * 					- Modifier(Class)
	 * 					- Parameters if parametric modifier supported
	 * 		Dependencies ->
	 * 			Symbol
	 *
	 *
	 *
	 * Требуется воссоздать ряд Объектных пулов
	 * Dependency
	 * Modifier
	 * AttributeChecker
	 *
	 * Продумать способ подгрузки и привязки по контекстам,
	 * дело в том что доступ к Объектным пулам в объектном стиле
	 * затруднителен в случае с мульти объектами Selector
	 *
	 * Статический тип подгрузки легкий в реализации,
	 * но подразумевает содержание пулов только в 1 экземпляре
	 * без возможности подмены в случае с хаками и фиксами и не
	 * дает того  потенциала подстановки событийной обработки под
	 * тот или иной пул(Пул как отдельная настроеная ветвь объектов
	 * под определенный спектр задач)
	 *
	 *
	 */
	class Selector implements \Traversable, \Iterator{

		/**
		 * @var Combination[]
		 */
		protected $combinations = [];

		/**
		 * @var Dependency[]
		 */
		protected $dependencies = [];

		/**
		 * @var bool
		 */
		protected $locked = false;


		/**
		 * @param Combination $combo
		 * @return bool
		 */
		public function isBeginner(Combination $combo){
			return $this->getBeginner() === $combo;
		}

		/**
		 * @return Combination
		 */
		public function getBeginner(){
			return isset($this->combinations[0]) ? $this->combinations[0] : null;
		}


		/**
		 * @param Combination $combo
		 * @return bool
		 */
		public function isTarget(Combination $combo){
			return $this->getTarget() === $combo;
		}

		/**
		 * @return Combination
		 */
		public function getTarget(){
			$c = count($this->combinations);
			return $c ? $this->combinations[count($this->combinations) - 1] : null;
		}


		/**
		 * @param Combination $combo [, $dependency, Combination $combo, ..., ...]
		 * @return $this
		 */
		public function set(Combination $combo){
			$numArgs = func_num_args();
			$collection = func_get_args();
			if($numArgs > 1 && $numArgs % 2 !== 1){
				throw new \InvalidArgumentException(
					'Selector.explode invalid arguments number must be odd number'
				);
			}

			$start = true;
			while($collection){
				if($start){
					$combo = array_shift($collection);
					if(!$combo instanceof Combination){
						throw new \InvalidArgumentException('Selector.explode Beginner Combo invalid');
					}
					$this->begin($combo);
					$start = false;
				}else{
					$dependency = array_shift($collection);
					$combo = array_shift($collection);
					if(!$combo instanceof Combination){
						throw new \InvalidArgumentException('Selector.explode Combo invalid');
					}
					if(!$dependency instanceof Dependency){
						throw new \InvalidArgumentException('Selector.explode Dependency invalid');
					}
					$this->add($dependency, $combo);
				}
			}
			return $this;
		}

		/**
		 * @param Combination $combo
		 * @return $this
		 */
		public function begin(Combination $combo){
			if($this->isLocked()){
				return $this;
			}
			$this->combinations = [];
			$this->dependencies = [];
			$this->combinations[] = $combo;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isLocked(){
			return $this->locked;
		}

		/**
		 * @param Dependency $dependency
		 * @param Combination $combo
		 * @return $this
		 */
		public function add(Dependency $dependency, Combination $combo){
			if($this->isLocked()){
				return $this;
			}
			if(!isset($this->combinations[0])){
				throw new \LogicException('Selector.add must be use after Selector.begin');
			}
			$this->combinations[] = $combo;
			$this->dependencies[] = $dependency;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function lock(){
			$this->locked = true;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function unlock(){
			$this->locked = false;
			return $this;
		}

		/**
		 * @return Dependency|null
		 */
		public function dependency(){
			$key = key($this->combinations) - 1;
			if($key >= 0 && isset($this->dependencies[$key])){
				return $this->dependencies[$key];
			}
			return null;
		}

		/**
		 * @return Combination
		 */
		public function combination(){
			return $this->current();
		}

		/**
		 * @return Combination
		 */
		public function current(){
			return $this->combinations[key($this->combinations)];
		}

		/**
		 *
		 */
		public function next(){
			next($this->combinations);
		}

		/**
		 * @return int
		 */
		public function key(){
			return key($this->combinations);
		}

		/**
		 * @return bool
		 */
		public function valid(){
			return isset($this->combinations[key($this->combinations)]);
		}

		/**
		 *
		 */
		public function rewind(){
			reset($this->combinations);
		}

		/**
		 * @param HTMLDocument $document
		 * @return \Jungle\XPlate\HTML\IElement[]
		 */
		public function __invoke(HTMLDocument $document){
			return $this->search($document);
		}

		/**
		 * @param HTMLDocument $document
		 * @return \Jungle\XPlate\HTML\IElement[]
		 */
		public function search(HTMLDocument $document){
			/**
			 * @var IElement[] $elements
			 */
			$elements = [];
			foreach($this as $i => $combination){
				$currentElements = [];
				$dependency = $this->dependency();
				if(!$dependency){
					$document->getElementsBy($combination);
				}else{
					foreach($elements as $element){
						$currentElements = array_merge($currentElements,$dependency->search($combination,$element));
					}
				}
				$elements = $currentElements;
			}
			return $elements;
		}


		public static function parse($selectorString){
			$regex = '@(\s*?(\*|([\w]+)?(#([\w\-]+))?(\.([\w\-]+))*?(::?([\w\d\-\(\)]+))*?(\[([\w\d\-\'\"=\*~!\$\^]+)\])*)\s*?([~\+>]))(\R?)@';




		}


	}
}