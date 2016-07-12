<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.02.2016
 * Time: 2:49
 */
namespace Jungle\FileSystem\Model\Manager\Adapter\Remote {

	use Jungle\FileSystem\Model\Manager\Adapter\Remote;

	/**
	 * Class SFTP
	 * @package Jungle\FileSystem\Model\Manager\Adapter\Remote
	 */
	class SFTP extends Remote{

		/**
		 * @param $path
		 * @return int
		 */
		public function filesize($path){
			// TODO: Implement filesize() method.
		}

		/**
		 * @param $path
		 * @return float
		 */
		public function disk_free_space($path){
			// TODO: Implement disk_free_space() method.
		}

		/**
		 * @param $path
		 * @return float
		 */
		public function disk_total_space($path){
			// TODO: Implement disk_total_space() method.
		}

		/**
		 * @param $path
		 * @param null $modifyTime
		 * @param null $accessTime
		 * @return mixed
		 */
		public function touch($path, $modifyTime = null, $accessTime = null){
			// TODO: Implement touch() method.
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function fileatime($path){
			// TODO: Implement fileatime() method.
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function filemtime($path){
			// TODO: Implement filemtime() method.
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function filectime($path){
			// TODO: Implement filectime() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_link($path){
			// TODO: Implement is_link() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_dir($path){
			// TODO: Implement is_dir() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_file($path){
			// TODO: Implement is_file() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_readable($path){
			// TODO: Implement is_readable() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_writable($path){
			// TODO: Implement is_writable() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_executable($path){
			// TODO: Implement is_executable() method.
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileperms($path){
			// TODO: Implement fileperms() method.
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileowner($path){
			// TODO: Implement fileowner() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function file_exists($path){
			// TODO: Implement file_exists() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function unlink($path){
			// TODO: Implement unlink() method.
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @param bool $recursive
		 * @return bool
		 */
		public function mkdir($path, $mod = 0777, $recursive = false){
			// TODO: Implement mkdir() method.
		}

		/**
		 * @param $path
		 * @return bool
		 * @throws \LogicException
		 */
		public function mkfile($path){
			// TODO: Implement mkfile() method.
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function rmdir($path){
			// TODO: Implement rmdir() method.
		}

		/**
		 * @param string $path
		 * @param int $owner
		 * @return bool
		 */
		public function chown($path, $owner){
			// TODO: Implement chown() method.
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @return bool
		 */
		public function chmod($path, $mod){
			// TODO: Implement chmod() method.
		}

		/**
		 * @param $path
		 * @param $group
		 * @return mixed
		 */
		public function chgrp($path, $group){
			// TODO: Implement chgrp() method.
		}

		/**
		 * @param string $path
		 * @param string $newPath
		 * @return bool
		 */
		public function rename($path, $newPath){
			// TODO: Implement rename() method.
		}

		/**
		 * @param string $path
		 * @param string $destination
		 * @return bool
		 */
		public function copy($path, $destination){
			// TODO: Implement copy() method.
		}

		/**
		 * @param $path
		 * @return array
		 */
		public function nodeList($path){
			// TODO: Implement nodeList() method.
		}

		/**
		 * @param $pattern
		 * @return array
		 */
		public function nodeListMatch($pattern){
			// TODO: Implement nodeListMatch() method.
		}

		/**
		 * @param $filePath
		 * @return string
		 */
		public function file_get_contents($filePath){
			// TODO: Implement file_get_contents() method.
		}

		/**
		 * @param $filePath
		 * @param $content
		 * @return mixed
		 */
		public function file_put_contents($filePath, $content){
			// TODO: Implement file_put_contents() method.
		}
	}
}

