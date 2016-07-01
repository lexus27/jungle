<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.05.2016
 * Time: 19:04
 */

include __DIR__ . DIRECTORY_SEPARATOR . 'lab' . DIRECTORY_SEPARATOR . 'loader.php';


$path = $loader->getNamespacePath('Jungle');

/**
 * Class ProjectStat
 */
abstract class FileSystemAnalyzer{

	/** @var  string[] */
	protected $base_directories = [];

	/** @var  array  */
	protected $besides_patterns = [];

	/** @var  array  */
	protected $besides_extensions = [];

	/** @var  array */
	protected $patterns = [];

	/** @var  array */
	protected $extensions = [];

	/** @var array  */
	protected $analyze_data;

	/** @var   */
	protected $current_base_dirname;

	/**
	 * @param array $directoryNames
	 * @param bool|false $merge
	 * @return $this
	 */
	public function setBaseDirectories(array $directoryNames, $merge = false){
		$this->base_directories = array_unique($merge?array_merge($this->base_directories, $directoryNames):$directoryNames);
		return $this;
	}

	/**
	 * @param array $patterns
	 * @param bool|false $merge
	 * @return $this
	 */
	public function setPatterns(array $patterns, $merge = false){
		$this->patterns = array_unique($merge?array_merge($this->patterns, $patterns):$patterns);
		return $this;
	}

	/**
	 * @param array $extensions
	 * @param bool|false $merge
	 * @return $this
	 */
	public function setExtensions(array $extensions, $merge = false){
		$this->extensions = array_unique($merge?array_merge($this->patterns, $extensions):$extensions);
		return $this;
	}



	/**
	 * @param array $patterns
	 * @param bool|false $merge
	 * @return $this
	 */
	public function setBesidesPatterns(array $patterns, $merge = false){
		$this->besides_patterns = array_unique($merge?array_merge($this->besides_patterns, $patterns):$patterns);
		return $this;
	}

	/**
	 * @param array $extensions
	 * @param bool|false $merge
	 * @return $this
	 */
	public function setBesidesExtensions(array $extensions, $merge = false){
		$this->besides_extensions = array_unique($merge?array_merge($this->besides_extensions, $extensions):$extensions);
		return $this;
	}


	/**
	 * @return array|null
	 */
	public function analyze(){
		$this->analyze_data = null;
		$this->getAnalyzeData();
		foreach($this->base_directories as $dirname){
			$this->current_base_dirname = $dirname;
			if($this->beforeBaseDirAnalyze($dirname)!==false){
				$this->analyzeDirectory($dirname);
				$this->afterBaseDirAnalyze($dirname);
			}
		}
		return $this->analyze_data;
	}

	/**
	 * @param $dirname
	 */
	protected function analyzeDirectory($dirname){
		$directory = opendir($dirname);
		while(($filename = readdir($directory))!==false){
			if ($filename == '.' || $filename == '..') continue;
			$pathname = $dirname . DIRECTORY_SEPARATOR . $filename;
			if(is_link($pathname)) continue;
			if(is_dir($pathname)){
				if($this->beforeDirAnalyze($pathname)!==false){
					$this->analyzeDirectory($pathname);
					$this->afterDirAnalyze($pathname);
				}
			}else{
				if($this->matchFilePath($pathname)){
					$this->onMatched($pathname);
				}
			}
		}
		closedir($directory);
	}

	/**
	 * @param $pathname
	 */
	abstract protected function onMatched($pathname);

	/**
	 * @param $pathname
	 * @return false|void
	 */
	protected function beforeDirAnalyze($pathname){}

	/**
	 * @param $pathname
	 * @return mixed
	 */
	protected function afterDirAnalyze($pathname){}

	/**
	 * @return mixed
	 */
	protected function &getAnalyzeData(){
		return $this->analyze_data;
	}


	/**
	 * @param $path
	 * @return bool
	 */
	public function matchFilePath($path){
		if(!$this->besides_patterns && !$this->besides_extensions && !$this->patterns && !$this->extensions){
			return true;
		}
		foreach($this->besides_patterns as $pattern){
			if(fnmatch($pattern, $path, FNM_PATHNAME)){
				return false;
			}
		}
		foreach($this->besides_extensions as $extension){
			if(strcasecmp(pathinfo($path, PATHINFO_EXTENSION), $extension)===0){
				return false;
			}
		}
		foreach($this->patterns as $pattern){
			if(fnmatch($pattern, $path, FNM_PATHNAME)){
				return true;
			}
		}
		foreach($this->extensions as $extension){
			if(strcasecmp(pathinfo($path, PATHINFO_EXTENSION), $extension)===0){
				return true;
			}
		}
		return false;
	}

	protected function beforeBaseDirAnalyze($dirname){}

	protected function afterBaseDirAnalyze($dirname){}

}

/**
 * Class ProjectAnalyzer
 */
class ProjectAnalyzer extends FileSystemAnalyzer{

	protected $old_files_limit = 5;

	protected $recent_files_limit = 5;

	/**
	 * @param $pathname
	 */
	protected function onMatched($pathname){
		$data = &$this->getAnalyzeData();
		$fp = fopen($pathname,'r');
		$multiDirs = count($this->base_directories)>1;
		while(($line = fgets($fp))!==false){
			$data['total_lines']++;
			if($multiDirs){
				$data['dirs'][$this->current_base_dirname]['total_lines']++;
			}
		}
		$size = filesize($pathname);
		$createTime = filectime($pathname);
		$updateTime = filemtime($pathname);

		$data['total_size']+=$size;
		$data['total_files']++;

		$this->handleFileTimeStack($data['old_files'],$createTime,$pathname,$this->old_files_limit);
		$this->handleFileTimeStack($data['recent_files'],$updateTime,$pathname,$this->recent_files_limit,false);

		if($multiDirs){
			$data['dirs'][$this->current_base_dirname]['total_size']+=$size;
			$data['dirs'][$this->current_base_dirname]['total_files']++;
			$this->handleFileTimeStack($data['dirs'][$this->current_base_dirname]['old_files'],$createTime,$pathname,$this->old_files_limit);
			$this->handleFileTimeStack($data['dirs'][$this->current_base_dirname]['recent_files'],$updateTime,$pathname,$this->recent_files_limit,false);
		}
	}

	/**
	 * @param $array
	 * @param $time
	 * @param $pathname
	 * @param int $limit
	 * @param bool $small
	 */
	protected function handleFileTimeStack(& $array, $time, $pathname, $limit = 5, $small = true){
		if(count($array) < $limit){
			$array[] = [
				'pathname' => $pathname,
				'time' => $time,
				'date' => date('d-m-Y (H:i:s)',$time)
			];
		}else{
			if(($small && $array[0]['time'] > $time) || (!$small && $array[0]['time'] < $time)){
				$array[0] = [
					'pathname' => $pathname,
					'time' => $time,
					'date' => date('d-m-Y (H:i:s)',$time)
				];
			}
		}
		usort($array,function($a,$b)use($small){
			if($a['time'] === $b['time']){
				return 0;
			}
			if($small){
				return $a['time'] < $b['time']? 1 : -1;
			}else{
				return $a['time'] > $b['time']? 1 : -1;
			}
		});
	}


	/**
	 * @return mixed
	 */
	protected function &getAnalyzeData(){
		if($this->analyze_data===null){
			$this->analyze_data = [
				'total_files' => 0,
				'total_lines' => 0,
				'total_size' => 0,
			    'old_files' => [],
				'recent_files' => [],
			];
			if(count($this->base_directories) > 1){
				$this->analyze_data['dirs'] = [];
				foreach($this->base_directories as $dirname){
					$this->analyze_data['dirs'][$dirname] = [
						'total_files'           => 0,
						'total_lines'           => 0,
						'total_size'            => 0,
						'old_files'             => [],
						'recent_files'          => [],
					];
				}
			}else{
				$this->analyze_data['dir'] = $this->base_directories[0];
			}
		}
		return $this->analyze_data;
	}
}

$classAnalyzer = new ProjectAnalyzer();
$classAnalyzer->setExtensions([
	'php'
]);
$classAnalyzer->setBaseDirectories([
	__DIR__ . DIRECTORY_SEPARATOR . 'lab'
]);
echo '<pre>',print_r($classAnalyzer->analyze(), 1),'</pre>';

$classAnalyzer->setBaseDirectories([
	$loader->getNamespacePath('Jungle') . DIRECTORY_SEPARATOR . 'Data'
]);
echo '<pre>',print_r($classAnalyzer->analyze(), 1),'</pre>';

$classAnalyzer->setBaseDirectories([
	$loader->getNamespacePath('Jungle') . DIRECTORY_SEPARATOR . 'XPlate'
]);
echo '<pre>',print_r($classAnalyzer->analyze(), 1),'</pre>';