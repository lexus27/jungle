<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.10.2016
 * Time: 23:16
 */
namespace Jungle\Util\Contents {
	
	/**
	 * Class Memory
	 * @package Jungle\Util\Contents
	 */
	class Memory implements ContentsAwareInterface{

		protected $contents;

		protected $media_type;

		protected $basename;


		/**
		 * Memory constructor.
		 * @param $basename
		 * @param $media_type
		 * @param $contents
		 */
		public function __construct($basename, $media_type, $contents){
			$this->basename = $basename;
			$this->media_type = $media_type;
			$this->contents = $contents;
		}

		/**
		 * @return mixed
		 */
		public function getMediaType(){
			return $this->media_type;
		}

		/**
		 * @return string
		 */
		public function getBasename(){
			return $this->basename;
		}

		/**
		 * @return mixed
		 */
		public function getSize(){
			return strlen($this->contents);
		}

		/**
		 * @return mixed
		 */
		public function getContents(){
			return $this->contents;
		}
	}
}

