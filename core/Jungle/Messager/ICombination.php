<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:28
 */
namespace Jungle\Messager {

	/**
	 * Interface IComplex
	 * @package Jungle\Messager
	 */
	interface ICombination{

		/**
		 * @param IContact $destination
		 * @return $this
		 */
		public function addDestination(IContact $destination);

		/**
		 * @param IContact $destination
		 * @return int|bool
		 */
		public function searchDestination(IContact $destination);

		/**
		 * @param IContact $destination
		 * @return $this
		 */
		public function removeDestination(IContact $destination);

		/**
		 * @return IContact[]
		 */
		public function getDestinations();

		/**
		 * @param IMessage $message
		 * @return $this
		 */
		public function setMessage(IMessage $message);

		/**
		 * @return IMessage
		 */
		public function getMessage();

	}
}

