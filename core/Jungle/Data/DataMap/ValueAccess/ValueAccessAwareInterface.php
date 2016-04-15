<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:08
 */
namespace Jungle\Data\DataMap\ValueAccess {

	/**
	 * Interface ValueAccessAwareInterface
	 * @package Jungle\Data\DataMap
	 */
	interface ValueAccessAwareInterface{

		/**
		 * @param $data
		 * @param $property
		 * @return mixed
		 */
		public function valueAccessGet($data, $property);

		/**
		 * @param $data
		 * @param $property
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $property, $value);

	}
}

