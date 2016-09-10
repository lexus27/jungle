<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.02.2016
 * Time: 15:43
 */
namespace Jungle\FileSystem\Model\Manager\Adapter {

	use Jungle\FileSystem\Model\Manager\Adapter;

	/**
	 * Class Local
	 * @package Jungle\FileSystem\Model\Manager\Adapter
	 */
	abstract class Local extends Adapter{

		/**
		 * @param $path
		 * @return int
		 */
		public function filesize($path){
			return filesize($this->absolute($path));
		}

		/**
		 * @param $path
		 * @return float
		 */
		public function disk_total_space($path){
			return disk_total_space($this->absolute($path));
		}

		/**
		 * @param $path
		 * @return float
		 */
		public function disk_free_space($path){
			return disk_free_space($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_link($path){
			return is_link($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_dir($path){
			return is_dir($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_file($path){
			return is_file($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_readable($path){
			return is_readable($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_writable($path){
			return is_writable($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_executable($path){
			return is_executable($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileperms($path){
			return fileperms($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileowner($path){
			return fileowner($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function file_exists($path){
			return file_exists($this->absolute($path));
		}

		/**
		 * @param $path
		 * @return bool
		 */
		public function mkfile($path){
			if(($fp = fopen($this->absolute($path), 'w'))){
				fclose($fp);
				return true;
			}
			return false;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function unlink($path){
			return unlink($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @param bool $recursive
		 * @return bool
		 */
		public function mkdir($path, $mod = 0777, $recursive = false){
			return mkdir($this->absolute($path),$mod,$recursive);
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function rmdir($path){
			return rmdir($this->absolute($path));
		}

		/**
		 * @param string $path
		 * @param int $owner
		 * @return bool
		 */
		public function chown($path, $owner){
			return chown($this->absolute($path),$owner);
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @return bool
		 */
		public function chmod($path, $mod){
			return chmod($this->absolute($path),$mod);
		}

		/**
		 * @param $path
		 * @param $group
		 * @return mixed
		 */
		public function chgrp($path, $group){
			return chgrp($this->absolute($path), $group);
		}

		/**
		 * @param string $path
		 * @param string $newPath
		 * @return bool
		 */
		public function rename($path, $newPath){
			return rename($this->absolute($path), $this->absolute($newPath));
		}

		/**
		 * @param string $path
		 * @param string $destination
		 * @return bool
		 */
		public function copy($path, $destination){
			return copy($this->absolute($path), $this->absolute($destination));
		}

		/**
		 * @param $path
		 * @return array
		 */
		public function nodeList($path){
			$a = [];
			foreach(scandir($this->absolute($path)) as $path){
				if(!in_array($path,['.','..'],true))$a[]= $path;
			}
			return $a;
		}

		/**
		 * @param $pattern
		 * @return array
		 */
		public function nodeListMatch($pattern){
			return (array)glob($this->absolute($pattern));
		}

		/**
		 * @param $path
		 * @param null $modifyTime
		 * @param null $accessTime
		 * @return mixed
		 */
		public function touch($path, $modifyTime = null, $accessTime = null){
			return touch($this->absolute($path),$modifyTime,$accessTime);
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function fileatime($path){
			return fileatime($this->absolute($path));
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function filemtime($path){
			return filemtime($this->absolute($path));
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function filectime($path){
			return filectime($this->absolute($path));
		}

		/**
		 * @param string $filePath
		 * @return string
		 */
		public function file_get_contents($filePath){
			return file_get_contents($this->absolute($filePath));
		}

		/**
		 * @param string $filePath
		 * @param string $content
		 * @return mixed
		 */
		public function file_put_contents($filePath, $content){
			return file_put_contents($this->absolute($filePath), $content);

		}

		/**
		 * @param Adapter $origin
		 * @param $originPath
		 * @param Adapter $destination
		 * @param $destinationPath
		 * @return bool|void
		 */
		public function transfer(
				Adapter $origin,        $originPath,
				Adapter $destination,   $destinationPath
		){
			if($origin instanceof Local && $destination instanceof Local){
				// Local TO Local transfer
				return $this->transferLocalToLocal($origin,$originPath,$destination,$destinationPath);
			}
			if($origin instanceof Local && $destination instanceof Remote){
				// Local TO Remote transfer
				return $this->transferLocalToRemote($origin,$originPath,$destination,$destinationPath);
			}
			if($origin instanceof Remote && $destination instanceof Local){
				// Remote TO Local transfer
				return $this->transferRemoteToLocal($origin,$originPath,$destination,$destinationPath);
			}
			if($origin instanceof Remote && $destination instanceof Remote){
				// Remote TO Remote transfer
				return $this->transferRemoteToRemote($origin,$originPath,$destination,$destinationPath);
			}
			return false;
		}

		/**
		 * @param Local $origin
		 * @param $originPath
		 * @param Local $destination
		 * @param $destinationPath
		 */
		public function transferLocalToLocal(
			Local $origin,        $originPath,
			Local $destination,   $destinationPath
		){

		}

		/**
		 * @param Local $origin
		 * @param $originPath
		 * @param Remote $destination
		 * @param $destinationPath
		 */
		public function transferLocalToRemote(
			Local $origin,        $originPath,
			Remote $destination,   $destinationPath
		){

		}

		/**
		 * @param Remote $origin
		 * @param $originPath
		 * @param Local $destination
		 * @param $destinationPath
		 */
		public function transferRemoteToLocal(
			Remote $origin,        $originPath,
			Local $destination,   $destinationPath
		){

		}

		/**
		 * TODO temporal drive
		 * @param Remote $origin
		 * @param $originPath
		 * @param Remote $destination
		 * @param $destinationPath
		 */
		public function transferRemoteToRemote(
			Remote $origin,        $originPath,
			Remote $destination,   $destinationPath
		){

		}
	}
}

