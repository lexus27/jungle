<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:11
 */
namespace Jungle\Util\Data\Foundation\Collection {

	/**
	 * Interface ExtendableInterface
	 * @package Jungle\Util\Data\Foundation\Collection
	 */
	interface ExtendableInterface{

		/**
		 * @param ExtendableInterface|null $ancestor
		 * @param bool $appliedIn
		 * @param bool $appliedInOld
		 * @return mixed
		 */
		public function setAncestor(ExtendableInterface $ancestor = null, $appliedIn = false, $appliedInOld = false);

		/**
		 * @return ExtendableInterface
		 */
		public function getAncestor();


		/**
		 * @return ExtendableInterface
		 */
		public function extend();


		/**
		 * @param ExtendableInterface $descendant
		 * @param bool $appliedIn
		 * @return mixed
		 */
		public function addDescendant(ExtendableInterface $descendant, $appliedIn  =false);

		/**
		 * @param ExtendableInterface $descendant
		 * @return mixed
		 */
		public function searchDescendant(ExtendableInterface $descendant);

		/**
		 * @param ExtendableInterface $descendant
		 * @param bool $appliedIn
		 * @return mixed
		 */
		public function removeDescendant(ExtendableInterface $descendant, $appliedIn  =false);

		/**
		 * @return ExtendableInterface[]
		 */
		public function getDescendants();


	}
}

