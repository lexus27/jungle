<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.02.2016
 * Time: 15:42
 */
namespace Jungle\FileSystem\Model\Manager {

	use Jungle\FileSystem\Model\Exception;
	use Jungle\FileSystem\Model\File;
	use Jungle\FileSystem\Model\Manager\Adapter\Remote;

	/**
	 * Class Adapter
	 * @package Jungle\FileSystem\Model\Manager
	 */
	abstract class Adapter{

		/** @var string */
		protected $root_path;


		public function transit(
			Adapter $origin,        $originPath,
			Adapter $destination,   $destinationPath
		){
			$content = $origin->file_get_contents($originPath);
			$destination->file_put_contents($destinationPath,$content);
		}

		/**
		 * @param null $root
		 * @throws Exception
		 */
		public function __construct($root = null){
			$this->setRootPath($root);
		}

		/**
		 * @param $path
		 * @throws Exception
		 */
		public function setRootPath($path){
			if($this->root_path!==null){
				throw new Exception("Root absolute already isset");
			}elseif($path){
				if(!$this->is_dir($path)){
					throw new Exception("Could not set root absolute to not exists directory");
				}
				$this->root_path = ltrim(dirname($path),'.\\/').$this->ds().basename($path);
			}else{
				$this->root_path = false;
			}
		}

		/**
		 * @return string
		 */
		public function getRootPath(){
			return $this->root_path;
		}

		/**
		 * @return string
		 */
		public function ds(){
			return DIRECTORY_SEPARATOR;
		}

		/**
		 * @param $path
		 * @return string
		 */
		public function absolute($path){
			return $this->root_path?($this->root_path . $this->ds() . ltrim($path,'\\/')):$path;
		}

		/**
		 * @param $path
		 * @return int
		 */
		abstract public function filesize($path);

		/**
		 * @param $path
		 * @return float
		 */
		abstract public function disk_free_space($path);

		/**
		 * @param $path
		 * @return float
		 */
		abstract public function dist_total_space($path);

		/**
		 * @param $path
		 * @param null $modifyTime
		 * @param null $accessTime
		 * @return mixed
		 */
		abstract public function touch($path, $modifyTime = null, $accessTime = null);

		/**
		 * @param $path
		 * @return mixed
		 */
		abstract public function fileatime($path);

		/**
		 * @param $path
		 * @return mixed
		 */
		abstract public function filemtime($path);

		/**
		 * @param $path
		 * @return mixed
		 */
		abstract public function filectime($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function is_link($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function is_dir($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function is_file($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function is_readable($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function is_writable($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function is_executable($path);

		/**
		 * @param string $path
		 * @return int
		 */
		abstract public function fileperms($path);

		/**
		 * @param string $path
		 * @return int
		 */
		abstract public function fileowner($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function file_exists($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function unlink($path);

		/**
		 * @param string $path
		 * @param int $mod
		 * @param bool $recursive
		 * @return bool
		 */
		abstract public function mkdir($path, $mod = 0777, $recursive = false);

		/**
		 * @param $path
		 * @return bool
		 * @throws \LogicException
		 */
		abstract public function mkfile($path);

		/**
		 * @param string $path
		 * @return bool
		 */
		abstract public function rmdir($path);

		/**
		 * @param string $path
		 * @param int $owner
		 * @return bool
		 */
		abstract public function chown($path, $owner);

		/**
		 * @param string $path
		 * @param int $mod
		 * @return bool
		 */
		abstract public function chmod($path, $mod);

		/**
		 * @param $path
		 * @param $group
		 * @return mixed
		 */
		abstract public function chgrp($path, $group);

		/**
		 * @param string $path
		 * @param string $newPath
		 * @return bool
		 */
		abstract public function rename($path, $newPath);

		/**
		 * @param string $path
		 * @param string $destination
		 * @return bool
		 */
		abstract public function copy($path, $destination);

		/**
		 * @param $path
		 * @return array
		 */
		abstract public function nodeList($path);

		/**
		 * @param $pattern
		 * @return array
		 */
		abstract public function nodeListMatch($pattern);

		/**
		 * @param string $filePath
		 * @return string
		 */
		abstract public function file_get_contents($filePath);

		/**
		 * @param string $filePath
		 * @param string $content
		 * @return mixed
		 */
		abstract public function file_put_contents($filePath, $content);
	}
}

