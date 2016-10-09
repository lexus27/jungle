<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 15:04
 */
namespace Jungle\Util\Communication\Connection\Stream {
	
	use Jungle\Util\Communication\Connection\Stream;

	/**
	 * Class Tmp
	 * @package Jungle\Util\Communication\Connection\Stream
	 */
	class Tmp extends Stream{

		/**
		 * @param array $config
		 * @return $this
		 */
		public function setConfig(array $config){
			$this->config = $config;
			return $this;
		}

		/**
		 * Open connection
		 * @return resource|bool
		 */
		protected function _connect(){
			return tmpfile();
		}

	}
}

