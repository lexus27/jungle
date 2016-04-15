<?php
define('PATH','I:'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'MP3'.DIRECTORY_SEPARATOR);

$i = 0;
$dirName = 'dir';
$section = 0;
foreach(glob(PATH.'*.mp3') as $path){
	$i++;
	$dir = PATH . $dirName . $section . DIRECTORY_SEPARATOR;
	if(!is_dir($dir)){
		mkdir($dir);
	}
	rename($path, $dir . basename($path));
	if($i>=20){
		$i = 0;
		$section++;
	}
}