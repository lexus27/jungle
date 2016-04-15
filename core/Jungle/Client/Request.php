<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 18:47
 */
namespace Jungle\Client {

	/**
	 * Class Request
	 * @package Jungle\Client
	 */
	abstract class Request{

		/**
		 * @param $key
		 */
		abstract public function getParam($key);

		/**
		 * @param $key
		 * @param $value
		 */
		abstract public function setParam($key, $value);

	}
}

