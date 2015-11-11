<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 17.03.2015
 * Time: 0:35
 */

namespace Jungle\Smart\Keyword;

/**
 * Class Factory
 * @package Jungle\Smart\Keyword
 */
class Factory {

	protected $rules = [];
	protected $function;

	public function __construct(callable $fn,array $rules=[]){
		$this->setFunction($fn);
		$this->setRules($rules);
	}

	public function setFunction(callable $fn){
		$this->function = $fn;
	}

	/**
	 * @param $identifier
	 * @return Keyword
	 */
	public function create($identifier){
		if($this->hasRule($identifier)){
			$rule = $this->getRule($identifier);
			$instance = call_user_func($rule,$identifier);
		}else{
			$instance = call_user_func($this->function,$identifier);
		}
		if(!$instance instanceof Keyword){
			throw new \LogicException('Keyword factory must create Keyword instances');
		}
		$instance->setIdentifier($identifier);
		return $instance;
	}

	/**
	 * @param $id
	 * @param callable $fn
	 */
	public function setRule($id,callable $fn){
		$this->rules[$id] = $fn;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function hasRule($id){
		return isset($this->rules[$id]);
	}

	/**
	 * @param $id
	 * @return null
	 */
	public function getRule($id){
		return $this->hasRule($id)?$this->rules[$id]:null;
	}

	/**
	 * @param array $rules
	 */
	public function setRules(array $rules){
		foreach($rules as $id => $fn){
			$this->setRule($id,$fn);
		}
	}

}