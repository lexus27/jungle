<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.09.2016
 * Time: 17:59
 */
namespace Jungle\User\AccessControl\Policy\Combiner {
	
	use Jungle\User\AccessControl\Policy\Combiner;

	/**
	 * Class EffectSameIfNotApplicable
	 * @package Jungle\User\AccessControl\Policy\Combiner
	 */
	class EffectSameIfNotApplicable extends Combiner{

		/**
		 *
		 */
		protected function onApplicable(){
			$this->earlyMatched(true,$this->result->getResult());
		}

		/**
		 * @return bool|string
		 */
		public function getEffect(){
			return $this->isCompliant()?$this->effect:$this->matchable_container->getEffect();
		}

	}
}

