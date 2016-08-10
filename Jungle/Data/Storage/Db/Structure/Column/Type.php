<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 01.03.2016
 * Time: 18:20
 */
namespace Jungle\Data\Storage\Db\Structure\Column {

	use Jungle\Data\Storage\Db\Structure\Column;
	use Jungle\Util\INamed;
	use Jungle\Util\Smart\Keyword\Keyword;

	/**
	 * Class Type
	 * @package Jungle\Data\Storage\Db\Structure\Column
	 */
	class Type extends Keyword implements INamed{


		/**
		 * @return int[]|int
		 */
		public function getMaxSize(){
			return $this->getOption('max_size',5000);
		}

		/**
		 * @return int[]|int
		 */
		public function getDefaultSize(){
			return $this->getOption('default_size',null);
		}

		/**
		 * @param int[]|int $size
		 * @return $this
		 */
		public function setDefaultSize($size){
			$this->setOption('default_size',$size);
			return $this;
		}


		/**
		 * @return int[]|int
		 */
		public function getMinSize(){
			return $this->getOption('min_size',0);
		}

		/**
		 * @param $size
		 * @return $this
		 */
		public function setMaxSize($size){
			$this->setOption('max_size',$size);
			return $this;
		}

		/***
		 * @param $size
		 * @return $this
		 */
		public function setMinSize($size){
			$this->setOption('min_size',$size);
			return $this;
		}


		/**
		 * @param $varType
		 * @return $this
		 */
		public function setVarType($varType){
			$this->setOption('var_type',$varType);
			return $this;
		}

		/**
		 * @return string
		 */
		public function getVarType(){
			return $this->getOption('var_type','string');
		}

		/**
		 * @param $size
		 * @return bool
		 */
		public function sizeInRange($size){
			return $this->getMinSize() <= $size &&
			       $this->getMaxSize() >= $size;
		}

		/**
		 * @param Column $column
		 */
		public function render(Column $column){

		}


		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->getIdentifier();
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->setIdentifier($name);
			return $this;
		}
	}
}

