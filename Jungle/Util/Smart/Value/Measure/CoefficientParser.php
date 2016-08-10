<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.11.2015
 * Time: 14:47
 */
namespace Jungle\Util\Smart\Value\Measure {

	abstract class CoefficientParser{

		/** @var  string */
		protected $regex = '@(?<factor>[\d]+\.?[\d]+)\s?((?<main>[\w]+)/?(?<second>[\w]+)?)@';

		/**
		 * @param string|null $regex
		 */
		public function __construct($regex = null){
			if($regex)$this->regex = $regex;
		}

		/**
		 * @param string $definition
		 * @param IUnit $unit
		 */
		abstract public function parse($definition,IUnit $unit);

		/**
		 * @param $definition
		 * @return bool
		 */
		protected function match($definition){
			if(preg_match($this->regex,$definition,$m)){
				return $m;
			}else{
				return false;
			}
		}

		/**
		 * @param $message
		 * @throws \LogicException
		 */
		protected function throwParserError($message){
			throw new \LogicException('Coefficient parser "'.preg_replace('@\\\\(\w+)$@','$1',get_class($this)).'" match error: '.$message);
		}

	}
}

