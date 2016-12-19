<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.02.2016
 * Time: 3:29
 */
namespace Jungle\FileSystem\Adapter {

	class Test extends Adapter{

		/**
		 * @var array
		 */
		protected $log = [];

		protected function log($method,$format,array $data = []){
			$format  = 'Called: '.$method.($format?' ' . $format:'');
			array_splice($data,0,0,[$format]);
			$this->log[] = call_user_func_array('sprintf',$data);
		}

		public function getLog(){
			return $this->log;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_link($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_dir($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_file($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_readable($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_writable($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_executable($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileperms($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return 0777;
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileowner($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return 0;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function file_exists($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function unlink($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @param bool $recursive
		 * @return bool
		 */
		public function mkdir($path, $mod = 0777, $recursive = false){
			$this->log(__FUNCTION__,'absolute: %s, mod: %o, recursive: %d',[$path,$mod, $recursive]);
			return true;
		}

		/**
		 * @param $path
		 * @return bool
		 * @throws \LogicException
		 */
		public function mkfile($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function rmdir($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return true;
		}

		/**
		 * @param string $path
		 * @param int $owner
		 * @return bool
		 */
		public function chown($path, $owner){
			$this->log(__FUNCTION__,'absolute: %s, owner: %s',[$path,$owner]);
			return true;
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @return bool
		 */
		public function chmod($path, $mod){
			$this->log(__FUNCTION__,'absolute: %s, mod: %o',[$path,$mod]);
			return true;
		}

		/**
		 * @param $path
		 * @param $group
		 * @return mixed
		 */
		public function chgrp($path, $group){
			$this->log(__FUNCTION__,'absolute: %s, group: %s',[$path, $group]);
			return true;
		}

		/**
		 * @param string $path
		 * @param string $newPath
		 * @return bool
		 */
		public function rename($path, $newPath){
			$this->log(__FUNCTION__,'absolute: %s, newpath: %s',[$path, $newPath]);
			return true;
		}

		/**
		 * @param string $path
		 * @param string $destination
		 * @return bool
		 */
		public function copy($path, $destination){
			$this->log(__FUNCTION__,'absolute: %s, destination: %s',[$path, $destination]);
			return true;
		}

		/**
		 * @param $path
		 * @return array
		 */
		public function nodeList($path){
			$this->log(__FUNCTION__,'absolute: %s',[$path]);
			return [];
		}

		/**
		 * @param $pattern
		 * @return array
		 */
		public function nodeListMatch($pattern){
			$this->log(__FUNCTION__,'pattern: %s',[$pattern]);
			return [];
		}

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
		 * @param string $filePath
		 * @return string
		 */
		public function file_get_contents($filePath){
			// TODO: Implement file_get_contents() method.
		}

		/**
		 * @param string $filePath
		 * @param string $content
		 * @return mixed
		 */
		public function file_put_contents($filePath, $content){
			// TODO: Implement file_put_contents() method.
		}
	}
}

