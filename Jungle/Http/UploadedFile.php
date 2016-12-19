<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 04.12.2016
 * Time: 17:20
 */
namespace Jungle\Http {
	
	use Jungle\FileSystem;
	use Jungle\Util\Contents\ContentsAwareInterface;

	/**
	 * Class File
	 * @package Jungle\Media
	 */
	class UploadedFile implements ContentsAwareInterface{

		public $path;

		public $param_name;

		public $name;

		public $mime_type;

		public $size;

		public $status;


		/**
		 * @return bool
		 */
		public function isSuccess(){
			return $this->status === UPLOAD_ERR_OK;
		}

		/**
		 * @return bool
		 */
		public function isClientError(){
			return in_array($this->status, [
				UPLOAD_ERR_INI_SIZE, // max file size exceeds by ini setting
				UPLOAD_ERR_FORM_SIZE, // max file size exceeds by form
				UPLOAD_ERR_PARTIAL,// file is not fully loaded
				UPLOAD_ERR_NO_FILE, // file is not present for upload
				UPLOAD_ERR_EXTENSION // file extension protection or not allowed
			], true);
		}

		/**
		 * @param $path
		 * @param bool $use_as_dirname
		 * @param bool $overwrite
		 * @throws \Exception
		 */
		public function moveTo($path, $use_as_dirname = false, $overwrite = false){
			if($this->status !== UPLOAD_ERR_OK){
				throw new \Exception('Move uploaded file error: "'.$this->getStatusText().'"');
			}

			if($use_as_dirname){
				$path = FileSystem::normalizePath($path . '/' . $this->name);
			}

			if(!is_uploaded_file($this->path)){
				throw new \Exception('UploadedFile can not be moved "'.$this->path.'" to "'.$path.'"');
			}

			if($overwrite && file_exists($path) && (!@chmod($path,0777) || !@unlink($path))){
				throw new \Exception('UploadedFile can not be moved "'.$this->path.'" to "'.$path.'" because overwrite error');
			}

			if(!@move_uploaded_file($this->path, $path)){
				throw new \Exception('UploadedFile can not be moved "'.$this->path.'" to "'.$path.'"');
			}
		}

		/**
		 * @return mixed
		 */
		public function getMediaType(){
			return $this->mime_type;
		}

		/**
		 * @return string
		 */
		public function getBasename(){
			return $this->name;
		}

		/**
		 * @return mixed
		 */
		public function getSize(){
			return $this->size;
		}

		/**
		 * @return mixed
		 */
		public function getContents(){
			return file_get_contents($this->path);
		}

		public function getStatusText($status = null){
			$error_strings = [
				UPLOAD_ERR_OK => 'Success', // client include
				UPLOAD_ERR_INI_SIZE => 'Max file size exceeded', // client include
				UPLOAD_ERR_FORM_SIZE => 'Max file size exceeded(form)', // client include
				UPLOAD_ERR_PARTIAL => 'UploadedFile is not fully loaded', // client include
				UPLOAD_ERR_NO_FILE => 'UploadedFile not present for upload', // client include
				UPLOAD_ERR_NO_TMP_DIR => 'Tmp directory on server not specified',
				UPLOAD_ERR_CANT_WRITE => 'Server can not save a uploaded file',
				UPLOAD_ERR_EXTENSION => 'Invalid file extension', // client include
			];
			return $error_strings[$status?:$this->status];
		}

		public function getStatusString($status = null){
			$error_strings = [
				UPLOAD_ERR_OK => 'UPLOAD_ERR_OK', // client include
				UPLOAD_ERR_INI_SIZE => 'UPLOAD_ERR_INI_SIZE', // client include
				UPLOAD_ERR_FORM_SIZE => 'UPLOAD_ERR_FORM_SIZE', // client include
				UPLOAD_ERR_PARTIAL => 'UPLOAD_ERR_PARTIAL', // client include
				UPLOAD_ERR_NO_FILE => 'UPLOAD_ERR_NO_FILE', // client include
				UPLOAD_ERR_NO_TMP_DIR => 'UPLOAD_ERR_NO_TMP_DIR',
				UPLOAD_ERR_CANT_WRITE => 'UPLOAD_ERR_CANT_WRITE',
				UPLOAD_ERR_EXTENSION => 'UPLOAD_ERR_EXTENSION', // client include
			];
			return $error_strings[$status?:$this->status];
		}

	}
}

