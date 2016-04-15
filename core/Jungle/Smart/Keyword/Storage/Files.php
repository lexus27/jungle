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
		if(!isset($c['extension'])){
			$c['extension'] = 'obj';
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

		$p = $this->getPathData($key);
		if(!is_dir($p[0]) && $this->config['dir_recursively']){
			mkdir($p[0],intval($this->config['dir_perms'],8),$this->config['dir_recursively']);
		}

		$dir = $p[0].DIRECTORY_SEPARATOR.$p[1].DIRECTORY_SEPARATOR;
		if(!is_dir($dir)){
			mkdir($dir,intval($this->config['dir_perms'],8),$this->config['dir_recursively']);
		}

		$path = implode(DIRECTORY_SEPARATOR,$p);

		$data = $this->prepareSave($key);
		if(!$this->saveFile($path, $data)){
			throw new \LogicException('Not Save');
		}else{
			chmod($path,intval($this->config['file_perms'],8));
		}
	}

	/**
	 * @param Keyword $key
	 * @return string
	 */
	protected function prepareSave(Keyword $key){
		return serialize($key);
	}

	/**
	 * @param $keyData
	 * @return mixed
	 */
	protected function prepareLoaded($keyData){
		return unserialize($keyData);
	}

	/**
	 * @param $path
	 * @return string
	 */
	protected function loadFile($path){
		return file_get_contents($path);
	}

	/**
	 * @param $path
	 * @param $data
	 * @return int
	 */
	protected function saveFile($path, $data){
		return file_put_contents($path,$data);
	}

	/**
	 * @param $key
	 * @return Keyword
	 */
	public function load($key){
		$path = implode(DIRECTORY_SEPARATOR,$this->getPathData($key));
		if(file_exists($path)){
			$object = $this->prepareLoaded($this->loadFile($path));
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
		$path = implode(DIRECTORY_SEPARATOR,$this->getPathData($key));
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
		$path = implode(DIRECTORY_SEPARATOR,$this->getPathData($key));
		if(file_exists($path)){
			unlink($path);
		}
		return true;
	}

	/**
	 * @TODO $matcher CLASS MATCHER::match($string)
	 * @param null $matcher
	 * @return array
	 */
	public function getList($matcher = null){
		$manager = $this->getManager();
		$base = rtrim($this->config['directory'],'\\/');
		$dir = $manager->getAlias();
		$path = $base . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
		$a = [];
		$files = glob($path.'*');
		if(!$manager->caseIsInsensitive()){
			foreach($files as $file){
				if(preg_match('@^([^\.]+)([.]+)'.preg_quote($this->getExtension(),'@').'$@',$file,$m)){
					$a[] = $m[2];
				}
			}
		}else{
			foreach($files as $file){
				if(preg_match('@^([.]+)'.preg_quote($this->getExtension(),'@').'$@',$file,$m)){
					$a[] = $m[1];
				}
			}
		}
		return $a;
	}

	/**
	 * @TODO $matcher CLASS MATCHER::match($string)
	 * @param $matcher
	 * @return mixed
	 */
	public function getCount($matcher = null){
		return count($this->getCount($matcher));
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
			$identifier = base64_encode($identifier).'.'.$identifier;
		}

		$identifier.='.'.$this->getExtension();
		return [rtrim($base,'\\/'),$dir,$identifier];
	}

	/**
	 * @return string
	 */
	protected function getExtension(){
		return isset($this->config['extension'])?$this->config['extension']:'.key.obj';
	}


}