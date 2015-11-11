<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 28.04.2015
 * Time: 22:37
 */

namespace Jungle\XPlate\HTML {


	use Jungle\XPlate\HTML\ElementFactory\Parser;
	use Jungle\XPlate\HTML\ElementFactory\Parser\XHTML;


	/**
	 * Class ParseErrorException
	 * @package Jungle\XPlate\HTML
	 */
	class ParseErrorException extends \LogicException{

	}

	/**
	 * Class ElementFactory
	 * @package Jungle\XPlate\HTML
	 */
	class ElementFactory{

		protected static $element_factory;

		/**
		 * @var Parser[]
		 */
		protected $parsers = [];

		/**
		 * @var callable
		 */
		protected $factory_method;


		/**
		 * @var bool
		 */
		protected $ws_before_waiting = false;

		/**
		 * @var null|Element
		 */
		protected $ws_after_waiter = null;


		/**
		 * @param callable $factory
		 */
		public function setFactoryMethod(callable $factory){
			$this->factory_method = $factory;
		}


		/**
		 * @param $plain
		 * @return Element
		 */
		public function instantiateElement($plain){
			if(!$this->factory_method){
				$this->factory_method = function (){
					return new Element();
				};
			}
			$element = call_user_func($this->factory_method, $plain);
			if(!$element instanceof Element){
				throw new \LogicException('Created element from factory method must be instance of Element');
			}
			return $element;
		}


		/**
		 * @param $string_definition
		 * @param null $requireAlias
		 * @return Element[]
		 * @throws \Exception
		 */
		public function interpret($string_definition, $requireAlias = null){
			$matched = null;
			$target_parser = null;

			if($requireAlias !== null){
				if(!is_array($requireAlias)){
					$requireAlias = [$requireAlias];
				}
				foreach($requireAlias as $alias){
					$parser = $this->getParser($alias);
					$matched = $parser->match($string_definition);
					if($matched !== false){
						$target_parser = $parser;
						break;
					}
				}

			}

			if(!$target_parser){
				foreach($this->parsers as $parser){
					$matched = $parser->match($string_definition);
					if($matched !== false){
						$target_parser = $parser;
						break;
					}
				}
			}

			if($target_parser){
				return $target_parser->interpret($string_definition, $matched);
			}else{
				throw new \Exception(
					'Произошла ошибка интерпретации одного фрагмента,
				авто-определение не обнаружило ни одного соответствия.' . ($requireAlias ? '
					Так-же по запросу псевдонимов ' . implode(', ', $requireAlias) . '
					аналогично не было ни одного соответствия' : '')
				);
			}
		}


		/**
		 * @param Parser $parser
		 */
		public function addParser(Parser $parser){
			$i = $this->searchParser($parser);
			if($i === false){
				if($this->getParser($parser->getAlias())){
					$this->throwParserAliasAlreadyExists($parser->getAlias());
				}
				$this->parsers[] = $parser;
				$parser->setFactory($this);
			}
		}

		/**
		 * @param Parser $parser
		 * @return mixed
		 */
		public function searchParser(Parser $parser){
			return array_search($parser, $this->parsers, true);
		}

		/**
		 * @param Parser $parser
		 */
		public function removeParser(Parser $parser){
			$i = $this->searchParser($parser);
			if($i !== false){
				array_splice($this->parsers, $i, 1);
				$parser->setFactory(null);
			}
		}

		/**
		 * @param $alias
		 * @return Parser|null
		 */
		public function getParser($alias){
			foreach($this->parsers as $parser){
				if($parser->getAlias() === $alias){
					return $parser;
				}
			}
			return null;
		}


		/**
		 * @param $argument
		 * @param null $row
		 * @return Element[]
		 * @throws \Exception
		 */
		public function decomposeRow($argument, & $row = null){
			if($row === null){
				$row = [];
			}
			if($argument instanceof Element){
				$row[] = $argument;
			}elseif(is_string($argument)){
				$model = $this->interpret($argument);
				$this->decomposeRow($model, $row);
			}elseif(is_array($argument)){
				foreach($argument as $i => & $v){
					$this->decomposeRow($v, $row);
				}
			}else{
				throw new \InvalidArgumentException('Element invalid input');
			}
			return $row;
		}

		/**
		 * @param $alias
		 */
		public function throwParserAliasAlreadyExists($alias){
			throw new \LogicException('Parser with alias "' . $alias . '" already exists');
		}

		/**
		 * @param $alias
		 */
		public function throwParseError($alias){
			throw new ParseErrorException("Парсер '{$alias}' не смог справиться с задачей");
		}


		/**
		 *
		 * @Plain-Whitespace-Control
		 *
		 */

		/**
		 * Call if passed plain only whitespace
		 */
		public function pcOnWhitespace(){
			if($this->pcHasWSAfterWaiting()){
				$this->pcSetWSAfter();
			}else if(!$this->pcHasWSBeforeWaiting()){
				$this->pcSetWSBeforeWait();
			}
		}

		/** Call if passed plain text exists
		 * @param Element $el
		 */
		public function pcOnNormal(Element $el){
			if($this->pcHasWSBeforeWaiting()){
				$this->pcWSBefore($el);
			}else if($this->pcHasWSAfterWaiting()){
				$this->pcClearWSAfterWaiter();
			}
		}

		/** Check on whitespaces flushed
		 * @return bool
		 */
		public function pcHasWSBeforeWaiting(){
			return null === $this->ws_after_waiter && $this->ws_before_waiting === true;
		}

		/** Check on before whitespace finded and after whitespace not yet defined
		 * @return bool
		 */
		public function pcHasWSAfterWaiting(){
			return null !== $this->ws_after_waiter;
		}

		/**
		 * Set whitespace flushed
		 */
		public function pcSetWSBeforeWait(){
			$this->ws_before_waiting = true;
		}

		/** Set before whitespace to passed Plain element and wait next whitespace for set after in passed Plain
		 * Plain fix for next whitespace
		 * @param Element $element
		 */
		public function pcWSBefore(Element $element){
			if($this->pcHasWSBeforeWaiting()){
				$element->setBeforeWhitespace();
				$this->ws_after_waiter = $element;
				$this->ws_before_waiting = false;
			}
		}

		/**
		 * Set after whitespace to fixed Plain element and flush whitespace state for next fragment
		 */
		public function pcSetWSAfter(){
			if($this->pcHasWSAfterWaiting()){
				$this->ws_after_waiter->setAfterWhitespace();
				$this->pcWsReady();
			}
		}

		/**
		 * Clear last Plain element. for not wait next whitespace exception
		 */
		public function pcClearWSAfterWaiter(){
			$this->ws_after_waiter = null;
		}

		/**
		 * Flush state to ready
		 */
		public function pcWsReady(){
			$this->ws_after_waiter = null;
			$this->ws_before_waiting = false;
		}

		/**
		 * @return ElementFactory
		 */
		public static function getFactory(){
			if(!self::$element_factory){
				self::$element_factory = new ElementFactory();
				self::$element_factory->addParser((new XHTML()));
			}
			return self::$element_factory;
		}

		/**
		 * @param ElementFactory $factory
		 */
		public static function setFactory(ElementFactory $factory){
			self::$element_factory = $factory;
		}

	}

}