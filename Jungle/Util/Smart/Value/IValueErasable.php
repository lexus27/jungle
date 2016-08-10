<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 16:52
 */
namespace Jungle\Util\Smart\Value {

	/**
	 * Interface IValueErasable
	 * @package Jungle\Util\Smart\Value
	 */
	interface IValueErasable{

		/**
		 * @return bool
		 */
		public function eraseValue();

	}
}

