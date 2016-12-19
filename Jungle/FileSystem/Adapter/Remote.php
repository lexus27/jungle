<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.02.2016
 * Time: 2:49
 */
namespace Jungle\FileSystem\Adapter {

	/**
	 * Class Remote
	 * @package Jungle\FileSystem\Adapter
	 */
	abstract class Remote extends Adapter{

		/**
		 *
		 */
		public function load($path){
			return $this->file_get_contents($path);
		}

	}
}

