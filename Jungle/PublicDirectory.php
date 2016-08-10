<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.07.2016
 * Time: 0:13
 */
namespace Jungle {

	/**
	 * Class PublicDirectory
	 * @package Jungle
	 */
	class PublicDirectory{

		/** @var  PublicDirectory */
		protected $parent;

		/** @var  PublicDirectory */
		protected $root;

		/** @var  string */
		protected $dirname;

		/**
		 * @param PublicDirectory $publicDirectory
		 * @return $this
		 */
		public function setParent(PublicDirectory $publicDirectory){
			$this->parent = $publicDirectory;
			return $this;
		}

		/**
		 * @return PublicDirectory
		 */
		public function getRoot(){
			if(!$this->root){
				if(!$this->parent){
					$this->root = $this;
				}else{
					$this->root = $this->parent->getRoot();
				}
			}
			return $this->root;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setDirname($name){
			$this->dirname = rtrim($name,'/\\');
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDirname(){
			if($this->parent){
				$dirname = $this->parent->getDirname();
				return $dirname . DIRECTORY_SEPARATOR . $this->dirname;
			}
			return $this->dirname;
		}

		/**
		 *
		 */
		public function actualize(){
			$dirname = $this->getDirname();
			if(!file_exists($dirname)){
				mkdir($dirname,0555,true);
			}
		}

		/**
		 * @param $uri
		 * @return string
		 */
		public function getFilename($uri){
			return $this->getDirname() . DIRECTORY_SEPARATOR . ltrim($uri,'/\\');
		}

		/**
		 * @param $filename
		 * @return string
		 */
		public function getUri($filename){
			$pubDirLength = strlen($this->getDirname());
			return substr($filename,0,$pubDirLength);
		}



		/**
		 * @param $uri
		 * @return string
		 */
		public function getAbsoluteFilename($uri){
			return $this->getRoot()->getDirname() . DIRECTORY_SEPARATOR . $this->getPublicDirname() . DIRECTORY_SEPARATOR . ltrim($uri,'/\\');
		}

		/**
		 * @param $filename
		 * @return string
		 */
		public function getAbsoluteUri($filename){
			return $this->getRoot()->getUri($filename);
		}

		/**
		 * @return string
		 */
		public function getPublicDirname(){
			if(!$this->parent){
				return '';
			}
			return $this->parent->getPublicDirname() . '/' . $this->dirname;
		}

	}
}

