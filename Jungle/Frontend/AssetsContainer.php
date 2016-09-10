<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.08.2016
 * Time: 14:49
 */
namespace Jungle\Frontend {

	/**
	 * Class AssetsContainer
	 * @package Jungle\Frontend
	 */
	class AssetsContainer{

		/** @var  string dirname root */
		protected $base_dirname;

		/**
		 * @param $dirname
		 */
		public function setBaseDirname($dirname){
			$this->base_dirname = $dirname;
		}

		/**
		 * @return string
		 */
		public function getBaseDirname(){
			return $this->base_dirname;
		}


		/**
		 * @param $absolute_path
		 * @param null $base_dirname
		 * @return bool|string
		 */
		public function relative($absolute_path, $base_dirname = null){
			if($base_dirname === null) $base_dirname = $this->base_dirname;
			if(strpos($absolute_path,$base_dirname) === 0){
				return substr($absolute_path, strlen($base_dirname));
			}else{
				return false;
			}
		}

		/**
		 * @param $relative_path
		 * @param null $base_dirname
		 * @return string
		 */
		public function absolute($relative_path, $base_dirname = null){
			if($base_dirname === null) $base_dirname = $this->base_dirname;
			return $base_dirname . DIRECTORY_SEPARATOR . ltrim($relative_path, '\/');
		}

	}
}

