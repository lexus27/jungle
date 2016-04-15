<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 7:26
 */
namespace Jungle\User\Access\ABAC\Policy\Combiner {

	use Jungle\User\Access\ABAC\Policy\Combiner;

	/**
	 *
	 * Присутствие такого-же эффекта и полное отсутствие противоположного,
	 *
	 * Class EffectSameOnly
	 * @package Jungle\User\Access\ABAC\Policy\Combiner
	 */
	class EffectSameSoft extends Combiner{

		/**
		 * @var bool
		 */
		protected static $default_compliant = true;

		protected function onApplicable(){
			if($this->effect !== $this->result->getResult()){
				$this->earlyMatched(true,$this->result->getResult());
			}
		}

		/**
		 * @return bool|string
		 */
		public function isCompliant(){
			if($this->stop){
				return parent::isCompliant();
			}else{
				return $this->_inResults($this->effect);
			}
		}

	}
}

