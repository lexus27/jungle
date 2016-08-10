<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.02.2016
 * Time: 22:09
 */
namespace Jungle\TypeHint\Rule\Builder {

	use Jungle\TypeHint;
	use Jungle\TypeHint\Rule;
	use Jungle\TypeHint\Rule\Builder;
	use Jungle\TypeHint\Rule\Complex;

	/**
	 * Class InlineBuilder
	 * @package Jungle\TypeHint\Rule\Builder
	 */
	class InlineBuilder extends Builder{

		/** @var string */
		protected $name = 'inline';


		/** @var string  */
		protected $regex = '@(\{ (?: (?: (?>[^{}]+) | (?R) )*) \}|[\w\\\\\s/\[\]]+) (\[[\w]*\])? (?:\s?\(([^)]+)\))? (?:\s?\#([^|$]+))?@sxmi';


		/**
		 * @param $definition
		 * @return bool
		 */
		public function checkSuit($definition){
			return is_string($definition);
		}

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return TypeHint\Rule\Complex
		 */
		public function build($definition, TypeHint $hinter){
			$r = trim($definition,'{}');
			if(preg_match($this->regex,$r,$m)){
				return $hinter->getBuilder('array')->build([
					'type'          => $m[1],
					'parameters'    => $m[2]?$this->parseParameters($m[2]):[],
					'comment'       => $m[3]?:null,
				],$hinter);
			}else{
				throw new \LogicException('Invalid input rule definition: '.$definition.'');
			}
		}

		public function parseParameters($string){
			$a = [];
			$pairs = explode(',',$string);
			foreach($pairs as $pair){
				$pair = explode(':',$pair);
				$c = count($pair);
				if($c===1){
					list($k) = $pair;
					$a[$k] = true;
				}elseif($c===2){
					list($k,$v) = $pair;
					$a[$k] = $v;
				}else{
					throw new \LogicException('Passed parameter pair is not a valid pair: {KEY}:{VALUE}');
				}
			}
			return $a;
		}

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return Complex
		 */
		public function buildComplex($definition, TypeHint $hinter){
			$definition = trim($definition,'{}');
			if(preg_match_all($this->regex,$definition,$_chunks)){
				$_chunks = $_chunks[0];
				$complex = new Complex();
				foreach($_chunks as & $r){
					$rule = $this->build($r,$hinter);
					if($rule instanceof Rule){
						$complex->addRule($rule);
					}
				}
				return $complex;
			}else{
				throw new \LogicException('Invalid input rule definition: '.$definition.'');
			}
		}
	}
}

