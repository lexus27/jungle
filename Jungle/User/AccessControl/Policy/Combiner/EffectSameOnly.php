<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 7:20
 */
namespace Jungle\User\AccessControl\Policy\Combiner {

	use Jungle\User\AccessControl\Policy\Combiner;

	/**
	 *
	 * Только такие-же эффекты
	 * Class EffectDuplicateOnly
	 * @package Jungle\User\AccessControl\Policy\Combiner
	 */
	class EffectSameOnly extends Combiner{

		/**
		 * @var bool
		 */
		protected static $default_compliant = true;

		protected function onIndeterminate(){
			$this->earlyMatched(false,$this->result->getResult());
		}

		protected function onNotApplicable(){
			$this->earlyMatched(false,$this->result->getResult());
		}

		/**
		 *
		 */
		protected function onApplicable(){
			if($this->effect !== $this->result->getResult()){
				$this->earlyMatched(true,$this->result->getResult());
			}
		}

	}
}

