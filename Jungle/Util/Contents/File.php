<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.10.2016
 * Time: 23:15
 */
namespace Jungle\Util\Contents {

	use Jungle\Util\ContentsAwareInterface;

	/**
	 * Class File
	 * @package Jungle\Util\Contents
	 */
	class File implements  ContentsAwareInterface{

		/** @var   */
		protected $path;

		/**
		 * File constructor.
		 * @param $path
		 */
		public function __construct($path){
			$this->path = $path;
		}

		/**
		 * @return mixed
		 */
		public function getMediaType(){
			return mime_content_type($this->path);
		}

		/**
		 * @return string
		 */
		public function getBasename(){
			return basename($this->path);
		}

		/**
		 * @return mixed
		 */
		public function getSize(){
			return filesize($this->path);
		}

		/**
		 * @return mixed
		 */
		public function getContents(){
			return file_get_contents($this->path);
		}
	}
}

