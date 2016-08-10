<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.05.2016
 * Time: 19:32
 */
namespace Model;


/**
 * Class Model
 * @package Model
 */
class Model{

	/** @var  State */
	protected $state;

	protected $dirty = [];

	protected $id;

	protected $name;

	protected $description;


	/**
	 * @param array $data
	 */
	public function initialize(array $data){
		$this->state = new State($data);
		$this->state->setSaved(true);
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function set($key, $value){
		if($this->{$key} !== $value){
			$this->dirty[$key] = true;
			$this->{$key} = $value;
		}
	}

	/**
	 * @param bool|false $saved
	 * @return $this
	 */
	public function rollback($saved = false){
		if($saved){
			$previous = $this->state->getPrevious();
			while($previous){
				if($previous->isSaved()){
					$this->state = $previous;
				}
				$previous = $this->state->getPrevious();
			}
		}else{
			$previous = $this->state->getPrevious();
			if($previous){
				$this->state = $previous;
			}
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	public function fix(){
		if(!empty($this->dirty)){
			$data = [];
			foreach($this->dirty as $key => $true){
				$data[$key] = $this->{$key};
			}
			$this->dirty = [];
			$state = new State($data, $this->state);
			$this->state = $state;
		}
		return $this;
	}

	public function save(){

		$this->fix();


		$data = $this->state->getData();




	}


}
class State{

	/** @var  State */
	protected $previous;

	/** @var   */
	protected $data;

	/** @var bool  */
	protected $saved = false;

	/**
	 * State constructor.
	 * @param $data
	 * @param State|null $previous
	 */
	public function __construct($data, State $previous = null){
		$this->data = $data;
		$this->previous = $previous;
	}

	/**
	 * @return State
	 */
	public function getPrevious(){
		return $this->previous;
	}

	/**
	 * @return bool
	 */
	public function isInitial(){
		return $this->saved && !$this->previous;
	}

	/**
	 * @return bool
	 */
	public function isSaved(){
		return $this->saved;
	}

	/**
	 * @param bool|true $saved
	 * @return $this
	 */
	public function setSaved($saved = true){
		$this->saved = $saved;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData(){
		if($this->previous && !$this->previous->isSaved()){
			$data = $this->previous->getData();
		}else{
			return $this->data;
		}
		return array_replace($this->data,$data);
	}

}