<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 18.05.2015
 * Time: 6:28
 */

namespace Jungle\XPlate\CSS\Selector;

/**
 * Class ModifierWrapper
 * @package Jungle\XPlate\CSS\Selector
 */
class ModifierQuery {

	/**
	 * @var Modifier
	 */
	protected $modifier;

	/**
	 * @var mixed
	 * Параметры модификатора типа: ":ntn-child(2)"
	 */
	protected $params;


	/**
	 * @param Modifier $modifier
	 * @return $this
	 */
	public function setModifier(Modifier $modifier){
		if($this->modifier !== $modifier){
			$this->modifier = $modifier;
		}
		return $this;
	}

	/**
	 * @return Modifier
	 */
	public function getModifier(){
		return $this->modifier;
	}

	/**
	 * @param $params
	 * @return $this
	 */
	public function setParams($params){
		$this->params = $params;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getParams(){
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function __toString(){
		$modifier = $this->getModifier();
		return
			($modifier->isPseudo() ? '::' : ':')
			. $modifier .
			($modifier->isParametric()?'('.$this->params.')':'');
	}

}