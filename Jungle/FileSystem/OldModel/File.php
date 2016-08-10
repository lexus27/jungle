<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.01.2016
 * Time: 4:03
 */
namespace Jungle\FileSystem\OldModel {

	use Jungle\FileSystem\OldModel;

	/**
	 * Class File
	 * @package Jungle\FileSystem\OldModel
	 */
	class File extends OldModel{

		/**
		 * @var string
		 */
		protected static $default_permissions = '444';

		/**
		 *
		 */
		protected function _create(){
			if(!($fp = @fopen($this->source_path, 'w'))){
				$e = error_get_last();
				throw new \LogicException(sprintf('Could not to create file %s , message %s',$fp, $e['message']));
			}else{
				fclose($fp);
			}
		}



		/**
		 * process delete
		 * @return void
		 */
		protected function _delete(){
			if(!@unlink($this->source_path)){
				$e = error_get_last();
				throw new \LogicException(sprintf('Error deleting file "%s", stopped with message: "%s"',$this->source_path, $e['message']));
			}
		}
	}
}

