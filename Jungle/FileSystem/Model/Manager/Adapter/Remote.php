<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.02.2016
 * Time: 2:49
 */
namespace Jungle\FileSystem\Model\Manager\Adapter {

	use Jungle\FileSystem\Model\Manager\Adapter;

	/**
	 * Class Remote
	 * @package Jungle\FileSystem\Model\Manager\Adapter
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

