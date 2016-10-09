<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.10.2016
 * Time: 12:57
 */
namespace Jungle\Util\Communication\Connection\Stream {
	
	use Jungle\Util\Communication\Connection\Stream;

	/**
	 * Class AccompanyWrap
	 * @package Jungle\Util\Communication\Connection\Stream
	 */
	class AccompanyWrap extends Stream{
		/**
		 * @param array $config
		 * @return $this
		 */
		public function setConfig(array $config){
			$this->config = array_replace([
				'resource' => null,
				'closable' => false,
			], $config);
			return $this;
		}


		/**
		 * Open connection
		 * @return resource|bool
		 */
		protected function _connect(){
			return $this->getOption('resource',null,true);
		}

		/**
		 *
		 */
		protected function _close(){
			if($this->getOption('closable',false)){
				fclose($this->connection);
			}
		}


	}
}

