<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 12.03.2015
 * Time: 0:04
 */

namespace Jungle\Smart\Keyword\Storage;


use Jungle\Smart\Keyword\Keyword;
use Jungle\Smart\Keyword\Storage;

class Files extends Storage{

	public function __construct(array $c = []){
		if(!isset($c['directory'])){
			throw new \InvalidArgumentException('Directory required');
		}
		if(!isset($c['dir_perms'])){
			$c['dir_perms'] = '0700';
		}
		if(!isset($c['dir_recursively'])){
			$c['dir_recursively'] = true;
		}
		if(!isset($c['file_perms'])){
			$c['file_perms'] = '0700';
		}
		if(!isset($c['file_ext'])){
			$c['file_ext'] = 'obj';
		}

		if(!$c['dir_recursively'] && !is_dir($c['directory'])){
			throw new \InvalidArgumentException('directory passed is not exists');
		}

		parent::__construct($c);
	}

	/**
	 * @param Keyword $key
	 * @return void
	 */
	public function save(Keyword $key){

		!defined('DS') && define('DS',DIRECTORY_SEPARATOR);

		$p = $this->getPathData($key);
		if(!is_dir($p[0]) && $this->config['dir_recursively']){
			mkdir($p[0],intval($this->config['dir_perms'],8),$this->config['dir_recursively']);
		}

		$dir = $p[0].DS.$p[1].DS;
		if(!is_dir($dir)){
			mkdir($dir,intval($this->config['dir_perms'],8),$this->config['dir_recursively']);
		}

		$path = implode(DS,$p);
		if(!file_put_contents($path,serialize($key))){
			throw new \LogicException('Not Save');
		}else{
			chmod($path,intval($this->config['file_perms'],8));
		}
	}

	/**
	 * @param $key
	 * @return Keyword
	 */
	public function load($key){
		!defined('DS') && define('DS',DIRECTORY_SEPARATOR);
		$path = implode(DS,$this->getPathData($key));
		if(file_exists($path)){
			$serialized = file_get_contents($path);
			$object = unserialize($serialized);
			if(!$object instanceof Keyword){
				return null;
			}
			$object->setDirty(false);
			return $object;
		}else{
			return null;
		}
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function has($key){
		!defined('DS') && define('DS',DIRECTORY_SEPARATOR);
		$path = implode(DS,$this->getPathData($key));
		if(file_exists($path)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function remove($key){
		!defined('DS') && define('DS',DIRECTORY_SEPARATOR);
		$path = implode(DS,$this->getPathData($key));
		if(file_exists($path)){
			unlink($path);
		}
		return true;
	}

	/**
	 * @param $key
	 * @return array
	 */
	protected function getPathData($key){
		$manager = $this->getManager();
		$base = $this->config['directory'];
		$dir = $manager->getAlias();
		$identifier = $key instanceof Keyword?$key->getIdentifier():$key;

		if(!$manager->caseIsInsensitive()){
			$identifier = md5($identifier).'_'.$identifier;
		}

		$identifier.='.'.$this->config['file_ext'];
		return [rtrim($base,'\\/'),$dir,$identifier];
	}
}