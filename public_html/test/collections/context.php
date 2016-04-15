<?php

/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.03.2016
 * Time: 15:17
 */
use Jungle\Data\Collection;


/**
 *
 * Контекст коллекции это способ сплочить много коллекций между собой
 *
 */

class CollectionContext{

	/** @var Collection[] */
	protected $collections = [];

	/**
	 * @param $collectionKey
	 * @param $collection
	 * @return $this
	 */
	public function set($collectionKey, $collection){
		$this->collections[$collectionKey] = $collection;
		return $this;
	}

	/**
	 * @param $collectionKey
	 * @return Collection|null
	 */
	public function get($collectionKey){
		return isset($this->collections[$collectionKey])?$this->collections[$collectionKey]:null;
	}

	/**
	 * @param $collectionKey
	 * @return bool
	 */
	public function has($collectionKey){
		return isset($this->collections[$collectionKey]);
	}

}

class Source{

	/**
	 * @param $identifier
	 */
	public function load($identifier){

	}

	public function loadByCriteria(){

	}

}

class CollectionView{

	protected $wrapper_template;

	protected $item_template;

	protected $collection;

}