<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.02.2016
 * Time: 22:09
 */
namespace Jungle\TypeHint\Rule\Builder {

	use Jungle\TypeHint;
	use Jungle\TypeHint\Rule\Builder;
	use Jungle\TypeHint\Rule\Complex;
	use Jungle\Util\Value\Massive;

	/**
	 * Class ArrayBuilder
	 * @package Jungle\TypeHint\Rule\Builder
	 */
	class ArrayBuilder extends Builder{

		/**
		 * @var string
		 */
		protected $name = 'array';

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return TypeHint\Rule
		 */
		public function build($definition, TypeHint $hinter){
			if(!isset($definition['type'])){
				throw new \LogicException('Error invalid input');
			}
			$definition = Massive::extend($definition,[
				'type'          => null,
				'parameters'    => null,
				'comment'       => null,
			]);
			return new TypeHint\Rule($definition['type'],$definition['parameters'],$definition['comment']);
		}

		/**
		 * @param $definition
		 * @return bool
		 */
		public function checkSuit($definition){
			return is_array($definition);
		}

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return Complex
		 */
		public function buildComplex($definition, TypeHint $hinter){
			if(!isset($definition[0]) && count($definition)){
				throw new \LogicException('Error invalid input');
			}
			$complex = new Complex();
			foreach($definition as $d){
				$complex->addRule(
					$this->build($d,$hinter)
				);
			}
			return $complex;
		}

	}
}

