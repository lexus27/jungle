<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.12.2016
 * Time: 21:29
 */
namespace Jungle\FileSystem {
	
	use Jungle\FileSystem;
	use Jungle\FileSystem\Adapter\Adapter;

	/**
	 * Class Basedir
	 * @package Jungle\FileSystem
	 */
	class Basedir extends Adapter{

		public $adapter;

		public $default_file_permissions = 0777;

		public $default_dir_permissions = 0777;

		/**
		 * Basedir constructor.
		 * @param Adapter $adapter
		 * @param null $default_file_permissions
		 * @param null $default_dir_permissions
		 */
		public function __construct(Adapter $adapter, $default_file_permissions = null, $default_dir_permissions = null){
			$this->adapter = $adapter;
			if($default_file_permissions!==null)$this->default_file_permissions = $default_file_permissions;
			if($default_dir_permissions!==null)$this->default_dir_permissions = $default_dir_permissions;
		}

		/**
		 * @param $path
		 * @param bool|true $normalize
		 * @return bool|void
		 */
		public function dir($path, $normalize = true){
			$a = $this->adapter;
			$path = trim($path,'\/');
			if($normalize){
				$path = FileSystem::normalizePath($path, true, $a->ds());
			}
			if(!$a->file_exists($path) || (!$a->is_file($path) || !$a->is_link($path))){
				$a->mkdir($path,$this->default_dir_permissions);
			}
			return true;
		}

		/**
		 * @param $path
		 * @param bool|true $force
		 * @param bool $ignore_denied
		 */
		public function delete($path, $ignore_denied = false, $force = false){
			$a = $this->adapter;
			if($a->file_exists($path)){
				if($is_dir = $a->is_dir($path)){
					foreach($a->nodeList($path) as $item){
						$this->delete($item, $force);
					}
				}

				if($a->is_writable($path)){
					if($is_dir) $a->rmdir($path);
					else $a->unlink($path);
				}else{
					if($force){
						$a->chmod($path,0777);
						if($is_dir) $a->rmdir($path);
						else $a->unlink($path);
					}elseif(!$ignore_denied){
						throw new \LogicException('Permission denied to write "'.$path.'"');
					}
				}
			}
		}


		/**
		 * @param $dirname
		 * @param callable|null $filter
		 * @param bool|true $dir_first
		 * @param bool|true $skip_dots
		 * @return \RecursiveCallbackFilterIterator|\RecursiveDirectoryIterator|\RecursiveIteratorIterator
		 */
		public function getRecursiveIterator($dirname, callable $filter = null, $dir_first = true, $skip_dots = true){
			if($dir_first){
				$recursive_iterator_options = \RecursiveIteratorIterator::SELF_FIRST;
			}else{
				$recursive_iterator_options = \RecursiveIteratorIterator::CHILD_FIRST;
			}
			if($skip_dots){
				$directory_iterator_options = \RecursiveDirectoryIterator::SKIP_DOTS;
			}else{
				$directory_iterator_options = null;
			}
			$iterator = new \RecursiveDirectoryIterator($dirname,$directory_iterator_options);
			if($filter){
				$iterator = new \RecursiveCallbackFilterIterator($iterator,$filter);
			}
			$iterator = new \RecursiveIteratorIterator($iterator, $recursive_iterator_options);
			return $iterator;
		}


		public function setRootPath($path,$auto_create = false){
			$this->adapter->setRootPath($path,$auto_create);
		}

		public function getRootPath(){
			return $this->adapter->root_path;
		}

		public function ds(){
			return $this->adapter->ds();
		}

		public function absolute($path){
			return $this->adapter->absolute($path);
		}

		public function relative($path){
			return $this->adapter->relative($path);
		}

		public function filesize($path){
			return $this->adapter->filesize($path);
		}

		public function disk_free_space($path){
			return $this->adapter->disk_free_space($path);
		}

		public function disk_total_space($path){
			return $this->adapter->disk_total_space($path);
		}

		public function touch($path, $modifyTime = null, $accessTime = null){
			return $this->adapter->touch($path, $modifyTime, $accessTime);
		}

		public function fileatime($path){
			return $this->adapter->fileatime($path);
		}

		public function filemtime($path){
			return $this->adapter->filemtime($path);
		}

		public function filectime($path){
			return $this->adapter->filectime($path);
		}

		public function is_link($path){
			return $this->adapter->is_link($path);
		}

		public function is_dir($path){
			return $this->adapter->is_dir($path);
		}

		public function is_file($path){
			return $this->adapter->is_file($path);
		}

		public function is_readable($path){
			return $this->adapter->is_readable($path);
		}
		public function is_writable($path){
			return $this->adapter->is_writable($path);
		}

		public function is_executable($path){
			return $this->adapter->is_executable($path);
		}

		public function fileperms($path){
			return $this->adapter->fileperms($path);
		}

		public function fileowner($path){
			return $this->adapter->fileowner($path);
		}

		public function file_exists($path){
			return $this->adapter->file_exists($path);
		}

		public function unlink($path){
			return $this->adapter->unlink($path);
		}

		public function mkdir($path, $mod = 0777, $recursive = false){
			return $this->adapter->mkdir($path, $mod, $recursive);
		}

		public function mkfile($path){
			return $this->adapter->mkfile($path);
		}

		public function rmdir($path){
			return $this->adapter->rmdir($path);
		}

		public function chown($path, $owner){
			return $this->adapter->chown($path, $owner);
		}

		public function chmod($path, $mod){
			return $this->adapter->chmod($path, $mod);
		}

		public function chgrp($path, $group){
			return $this->adapter->chgrp($path, $group);
		}

		public function rename($path, $newPath){
			return $this->adapter->rename($path, $newPath);
		}

		public function copy($path, $destination){
			return $this->adapter->copy($path, $destination);
		}

		public function nodeList($path){
			return $this->adapter->nodeList($path);
		}

		public function nodeListMatch($pattern){
			return $this->adapter->nodeListMatch($pattern);
		}

		public function file_get_contents($filePath){
			return $this->adapter->file_get_contents($filePath);
		}

		public function file_put_contents($filePath, $content){
			return $this->adapter->file_put_contents($filePath, $content);
		}
	}
}

