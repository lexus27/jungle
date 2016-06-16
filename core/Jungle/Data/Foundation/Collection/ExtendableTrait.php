<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:12
 */
namespace Jungle\Data\Foundation\Collection {

	/**
	 * Class ExtendableTrait
	 * @package Jungle\Data\Foundation\Collection
	 */
	trait ExtendableTrait{

		/** @var  ExtendableInterface */
		protected $ancestor;

		/** @var  ExtendableInterface[] */
		protected $descendants = [ ];

		/**
		 * @return mixed
		 */
		public function extend(){
			/** @var ExtendableInterface $descendant */
			$descendant = clone $this;
			$this->_onDelivery($descendant);
			return $descendant;
		}

		/**
		 * @param ExtendableInterface|null|null $ancestor
		 * @param bool $appliedInNew
		 * @param bool $appliedInOld
		 * @return mixed
		 */
		public function setAncestor(ExtendableInterface $ancestor = null, $appliedInNew = false, $appliedInOld = false){
			/** @var ExtendableInterface|ExtendableTrait $this */
			$old = $this->ancestor;
			if($old !== $ancestor){
				$this->ancestor = $ancestor;
				if($old && !$appliedInOld){
					$old->removeDescendant($this, true);
				}
				if($ancestor && !$appliedInNew){
					$ancestor->addDescendant($this, true);
				}
			}
			$this->ancestor = $ancestor;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getAncestor(){
			return $this->ancestor;
		}

		/**
		 * @param ExtendableInterface $descendant
		 */
		protected function _onDelivery(ExtendableInterface $descendant){
			$descendant->setAncestor($this);
		}

		/**
		 * @param ExtendableInterface|ExtendableTrait $descendant
		 * @param bool $applied
		 * @return $this
		 */
		public function addDescendant(ExtendableInterface $descendant, $applied = false){
			if(array_search($descendant, $this->descendants, true) === false){
				$this->descendants[] = $descendant;
				if(!$applied) $descendant->setAncestor($this, true);
			}
			return $this;
		}

		/**
		 * @param ExtendableInterface|ExtendableTrait $descendant
		 * @return mixed
		 */
		public function searchDescendant(ExtendableInterface $descendant){
			return array_search($descendant, $this->descendants, true);
		}

		/**
		 * @param ExtendableInterface|ExtendableTrait $descendant
		 * @param bool $applied
		 * @return $this
		 */
		public function removeDescendant(ExtendableInterface $descendant, $applied = false){
			if(($i = array_search($descendant, $this->descendants, true)) !== false){
				array_splice($this->descendants, $i, 1);
				if(!$applied) $descendant->setAncestor(null, true, true);
			}
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDescendants(){
			return $this->descendants;
		}


	}

}

