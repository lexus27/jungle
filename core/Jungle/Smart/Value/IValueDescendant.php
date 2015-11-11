<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 11:34
 */

namespace Jungle\Smart\Value {



	/**
	 * Interface IValueExtendable
	 * @package Jungle\Smart\Value
	 */
	interface IValueDescendant extends IValue{

		/**
		 * @return $this
		 */
		public function setAncestor();

		/**
		 * @return IValue
		 */
		public function getAncestor();

	}
}