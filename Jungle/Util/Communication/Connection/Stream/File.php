<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 15:02
 */
namespace Jungle\Util\Communication\Connection\Stream {
	
	use Jungle\Util\Communication\Connection\Stream;

	/**
	 * Class File
	 * @package Jungle\Util\Communication\Connection\Stream
	 */
	class File extends Stream{

		const MODE_READ            = 'r';
		const MODE_READ_CREATE     = 'r+';

		const MODE_WRITE            = 'w';
		const MODE_WRITE_CREATE     = 'w+';

		const MODE_1     = 'a';
		const MODE_1_1     = 'a+';

		/**
		 * @param array $config
		 * @return $this
		 */
		public function setConfig(array $config){
			$this->config = array_replace([
				'path'          => null,
				'mode'          => null,
				'context'       => null,
			], $config);
			return $this;
		}


		/**
		 * Open connection
		 * @return resource|bool
		 */
		protected function _connect(){
			return fopen(
				$this->getOption('path',null,true),
				$this->getOption('mode',null,true),
				null,
				$this->getOption('context')
			);
		}
	}
}

