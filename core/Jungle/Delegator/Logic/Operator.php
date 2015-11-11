<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Date: 09.04.2015
 * Time: 16:41
 */

namespace Jungle\Logic;

/**
 * Class Operator
 * @package Jungle\Logic
 */
class Operator {

	/**
	 * @var callable
	 */
	protected $handler;

	/**
	 * @param callable $handler
	 */
	public function setHandler(callable $handler){
		$this->handler = $handler;
	}

	/**
	 * @return mixed
	 */
	public function getHandler(){
		return $this->handler;
	}

	/**
	 * @param Condition $condition
	 * @return mixed
	 */
	public function check(Condition $condition){
		return call_user_func($this->getHandler(), $condition);
	}

}