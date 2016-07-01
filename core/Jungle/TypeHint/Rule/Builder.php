<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.02.2016
 * Time: 22:06
 */
namespace Jungle\TypeHint\Rule {

	use Jungle\TypeHint;
	use Jungle\TypeHint\Rule;
	use Jungle\Util\INamed;

	/**
	 * Class Builder
	 * @package Jungle\TypeHint\Rule
	 */
	abstract class Builder implements INamed{

		protected $name;

		public function setName($name){
			throw new \LogicException('setName: Not applicable to default builders');
		}

		public function getName(){
			return $this->name;
		}

		/**
		 * @param $definition
		 * @return bool
		 */
		abstract public function checkSuit($definition);

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return Rule
		 */
		abstract public function build($definition, TypeHint $hinter);

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return Complex
		 */
		abstract public function buildComplex($definition, TypeHint $hinter);

	}
}

