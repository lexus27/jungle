<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.03.2016
 * Time: 13:53
 */


/**
 * Interface KeyboardAxisInterface
 */
interface KeyboardAxisInterface extends Countable{

	/**
	 * @return int
	 */
	public function count();

	/**
	 * @return int
	 */
	public function getMaxSize();


	/**
	 * @return KeyboardAxisInterface|null
	 */
	public function getParent();

	/**
	 * @param KeyboardAxisInterface $axis
	 * @param bool $appliedInNew
	 * @param bool $appliedInOld
	 * @return $this
	 */
	public function setParent(KeyboardAxisInterface $axis = null,$appliedInNew = false, $appliedInOld = false);


	/**
	 * @return null|KeyboardAxisInterface[]
	 */
	public function getChildren();

	/**
	 * @param KeyboardAxisInterface $item
	 * @param bool $appliedIn
	 * @return $this
	 */
	public function add(KeyboardAxisInterface $item,$appliedIn = false);

	/**
	 * @param KeyboardAxisInterface $item
	 * @param bool $appliedIn
	 * @return $this
	 */
	public function remove(KeyboardAxisInterface $item,$appliedIn = false);

	/**
	 * @param KeyboardAxisInterface $item
	 * @return $this
	 */
	public function search(KeyboardAxisInterface $item);

	/**
	 * @return bool
	 */
	public function isLeaf();

}


/**
 * Class Keyboard
 */
class Keyboard extends KeyboardAxis{

	/**
	 * @param KeyboardAxis $row
	 * @return $this
	 */
	public function addRow(KeyboardAxis $row){
		return $this->add($row);
	}

	/**
	 * @param KeyboardAxis $row
	 * @return $this
	 */
	public function removeRow(KeyboardAxis $row){
		return $this->remove($row);
	}

	/**
	 * @param KeyboardAxis $row
	 * @return mixed
	 */
	public function searchRow(KeyboardAxis $row){
		return $this->search($row);
	}

}
$Keyboard = new Keyboard(2,[
	(new KeyboardAxis(25,[

		(new KeyboardButton('ESC')),
		(new KeyboardButton(null)),
		(new KeyboardButton('F1')),
		(new KeyboardButton('F2')),
		(new KeyboardButton('F3')),
		(new KeyboardButton('F4')),
		(new KeyboardButton(null)),
		(new KeyboardButton('А5')),
		(new KeyboardButton('А6')),
		(new KeyboardButton('А7')),
		(new KeyboardButton('А8')),
		(new KeyboardButton(null)),
		(new KeyboardButton('F9')),
		(new KeyboardButton('F10')),
		(new KeyboardButton('F11')),
		(new KeyboardButton('F12')),
		(new KeyboardButton(null)),
		(new KeyboardButton('PSS')),
		(new KeyboardButton('SCROLL_LOCK')),
		(new KeyboardButton('PAUSE')),

		(new KeyboardButton(null)),
		(new KeyboardButton(null)),
		(new KeyboardButton(null)),
		(new KeyboardButton(null)),
		(new KeyboardButton(null)),

	])),
	(new KeyboardAxis(25,null)),

	(new KeyboardAxis(3,[
		(new KeyboardAxis(15,[
			(new KeyboardButton('~')),
			(new KeyboardButton('1')),
			(new KeyboardButton('2')),
			(new KeyboardButton('3')),
			(new KeyboardButton('4')),
			(new KeyboardButton('5')),
			(new KeyboardButton('6')),
			(new KeyboardButton('7')),
			(new KeyboardButton('8')),
			(new KeyboardButton('9')),
			(new KeyboardButton('0')),
			(new KeyboardButton('-')),
			(new KeyboardButton('=')),
			(new KeyboardButton('\\')),
			(new KeyboardButton('BACK_SPACE')),
		])),
		(new KeyboardAxis(3,[

		])),
		(new KeyboardAxis(3,[

		]))
	]))

]);



class KeyboardButton implements KeyboardAxisInterface, \Jungle\Basic\INamed{

	/** @var  KeyboardAxisInterface|null */
	protected $parent;

	protected $name;

	protected $empty = false;

	/**
	 * @param $name
	 */
	public function __construct($name){
		if($name === null){
			$this->empty = true;
		}else{
			$this->setName($name);
		}

	}

	/**
	 * Получить имя объекта
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Выставить имя объекту
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}
	/**
	 * @return int
	 */
	public function count(){
		return 0;
	}

	/**
	 * @return int
	 */
	public function getMaxSize(){
		return 0;
	}
	/**
	 * @return KeyboardAxisInterface|null
	 */
	public function getParent(){
		return $this->parent;
	}

	/**
	 * @param KeyboardAxisInterface $axis
	 * @param bool $appliedInNew
	 * @param bool $appliedInOld
	 * @return $this
	 */
	public function setParent(KeyboardAxisInterface $axis = null,$appliedInNew = false, $appliedInOld = false){
		$old = $this->parent;
		if($old !== $axis){
			$this->parent = $axis;
			if($axis && !$appliedInNew){
				$axis->add($this,true);
			}
			if($old && !$appliedInOld){
				$old->remove($this,true);
			}
		}
		return $this;
	}

	/**
	 * @return null|KeyboardAxisInterface[]
	 */
	public function getChildren(){
		return null;
	}

	/**
	 * @param KeyboardAxisInterface $item
	 * @param bool $appliedIn
	 * @return $this
	 */
	public function add(KeyboardAxisInterface $item, $appliedIn = false){
		return $this;
	}

	/**
	 * @param KeyboardAxisInterface $item
	 * @param bool $appliedIn
	 * @return $this
	 */
	public function remove(KeyboardAxisInterface $item, $appliedIn = false){
		return $this;
	}

	/**
	 * @param KeyboardAxisInterface $item
	 * @return $this
	 */
	public function search(KeyboardAxisInterface $item){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isLeaf(){
		return true;
	}

}


class KeyboardAxis implements KeyboardAxisInterface{

	/** @var  int */
	protected $max_size = 1;

	/** @var array  */
	protected $items = [];

	/** @var KeyboardAxis  */
	protected $parent;

	/**
	 * @param $max_size
	 * @param array $items
	 */
	public function __construct($max_size,array $items = []){
		$this->max_size = $max_size;
		$this->items = $items;
	}

	/**
	 * @return int
	 */
	public function count(){
		return count($this->items);
	}


	/**
	 * @return KeyboardAxisInterface|null
	 */
	public function getParent(){
		return $this->parent;
	}

	/**
	 * @param KeyboardAxisInterface $axis
	 * @param bool $appliedInNew
	 * @param bool $appliedInOld
	 * @return $this
	 */
	public function setParent(KeyboardAxisInterface $axis = null,$appliedInNew = false, $appliedInOld = false){
		$old = $this->parent;
		if($old !== $axis){
			$this->parent = $axis;
			if($axis && !$appliedInNew){
				$axis->add($this,true);
			}
			if($old && !$appliedInOld){
				$old->remove($this,true);
			}
		}
		return $this;
	}

	/**
	 * @return null|KeyboardAxisInterface[]
	 */
	public function getChildren(){
		if($this->isLeaf()){
			return null;
		}
		return $this->items;
	}

	/**
	 * @param KeyboardAxisInterface $item
	 * @param bool $appliedIn
	 * @return $this
	 */
	public function add(KeyboardAxisInterface $item,$appliedIn = false){
		if($this->isLeaf()){
			return $this;
		}
		if($this->getMaxSize() <= count($this->items)){
			return $this;
		}
		if($this->search($item)===false){
			$this->items[] = $item;
			if(!$appliedIn){
				$item->setParent($this,true);
			}
		}
		return $this;
	}

	/**
	 * @param KeyboardAxisInterface $item
	 * @param bool $appliedIn
	 * @return $this
	 */
	public function remove(KeyboardAxisInterface $item,$appliedIn = false){
		if($this->isLeaf()){
			return $this;
		}
		if(($i = $this->search($item))!==false){
			array_splice($this->items,$i,1);
			if(!$appliedIn){
				$item->setParent(null,true,true);
			}
		}
		return $this;
	}

	/**
	 * @param $item
	 * @return mixed
	 */
	public function search(KeyboardAxisInterface $item){
		if($this->isLeaf()){
			return false;
		}
		return array_search($item,$this->items,true);
	}

	/** @return bool */
	public function isLeaf(){
		return false;
	}

	/**
	 * @return mixed
	 */
	public function getMaxSize(){
		return $this->max_size;
	}

}