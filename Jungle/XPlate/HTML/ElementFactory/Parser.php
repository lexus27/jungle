<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 28.04.2015
 * Time: 22:42
 */

namespace Jungle\XPlate\HTML\ElementFactory {

	use Jungle\XPlate\HTML\ElementFactory;
	use Jungle\XPlate\HTML\ParseErrorException;

	/**
	 * Class Parser
	 * @package Jungle\XPlate2\HTML\ElementFactory
	 */
	abstract class Parser{

		/**
		 * @var ElementFactory
		 */
		protected $factory;

		/**
		 * @var string
		 */
		protected $alias;

		/**
		 * @var callable
		 */
		protected $checker;

		/**
		 * @var
		 */
		protected $regex;

		/**
		 * @param $alias
		 */
		public function __construct($alias){
			if(!$alias){
				throw new \InvalidArgumentException('Alias не может быть пустым');
			}
			$this->setAlias($alias);
		}

		/**
		 * @param $alias
		 * @return $this
		 */
		public function setAlias($alias){
			if($this->alias !== $alias){
				if($this->factory && $this->factory->getParser($alias)){
					$this->factory->throwParserAliasAlreadyExists($alias);
				}
				$this->alias = $alias;
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAlias(){
			return $this->alias;
		}

		/**
		 * @param ElementFactory $factory
		 */
		public function setFactory(ElementFactory $factory = null){
			$old = $this->factory;
			if($old !== $factory){
				$this->factory = $factory;
				if($factory){
					try{
						$factory->addParser($this);
					}catch(\LogicException $e){
						$this->factory = $old;
						throw $e;
					}
				}
				if($old){
					$old->removeParser($this);
				}
			}

		}

		/**
		 * @return ElementFactory
		 */
		public function getFactory(){
			return $this->factory;
		}

		/**
		 * @return mixed
		 */
		public function getRegex(){
			return $this->regex;
		}

		/**
		 * @param callable $checker
		 */
		public function setChecker(callable $checker){
			$this->checker = $checker;
		}

		/**
		 * @param $string_definition
		 * @return bool
		 */
		public function match($string_definition){
			if(!$this->checker){
				throw new \LogicException(
					'В парсере с псевдонимом "' . $this->getAlias() . '" не установлен checker ,
				 пожалуйста воспользуйтесь методом setChecker'
				);
			}
			return boolval(call_user_func($this->checker, $string_definition));
		}

		/**
		 * @param $string_definition
		 * @param null $matchedData
		 * @return \Jungle\XPlate\HTML\Element[]
		 */
		public function interpret($string_definition, $matchedData = null){
			$this->getFactory()->pcWsReady();
			return $this->processParse($string_definition, $matchedData);
		}

		abstract protected function processParse(& $string_definition, $matchedData = null, $depth = 0);

		abstract protected function findElementForTag($tagName);

		/**
		 * @param $attributes_string
		 * @return array
		 */
		protected function parseAttributes($attributes_string){
			$attributes = [];
			if(preg_match_all('@((\w+)=("[^"]+"|\'[^\']+\'|\S+)|\w+)@', $attributes_string, $matches)){
				foreach($matches[0] as $match){
					$k = null;
					$v = null;
					$raw = explode('=', preg_replace('@[\s\r\n\t]+@', ' ', $match));
					if($raw[0]){
						$k = strtolower(trim($raw[0]));
						$v = isset($raw[1]) ? trim($raw[1], '"\' ') : true;
						if($v === 'true'){
							$v = true;
						}elseif($v === 'false'){
							$v = false;
						}
						$attributes[$k] = $v;
					}
				}
			}
			return $attributes;
		}

		/**
		 * @param $content
		 * @return int
		 */
		public function isWhitespace($content){
			return preg_match('@^[\s\r\n\t]+$@', $content);
		}

		protected function throwParseError(){
			throw new ParseErrorException();
		}


	}

}