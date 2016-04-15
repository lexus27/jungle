<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 7:41
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections {

	/**
	 * Interface IRegistryRead
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface IRegistryRead{

		/**
		 * @param $key
		 * @return mixed
		 */
		function get($key);

		/**
		 * @param $key
		 * @return bool
		 */
		function has($key);

	}
}

