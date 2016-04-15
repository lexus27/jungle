<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 7:41
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections {

	/**
	 * Interface IRegistryWrite
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface IRegistryWrite{

		/**
		 * @param $k
		 * @param $v
		 * @return $this
		 */
		function set($k,$v);

		/**
		 * @param $k
		 * @return $this
		 */
		function remove($k);

	}
}

