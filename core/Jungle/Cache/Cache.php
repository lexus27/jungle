<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 11:10
 */
namespace Jungle\Cache {

	/**
	 * Interface Cache
	 * @package Jungle\Cache
	 *
	 */
	interface Cache{

		public function get();

		public function save();

		public function isExists();

	}
}

