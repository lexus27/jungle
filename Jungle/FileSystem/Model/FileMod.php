<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 01.02.2016
 * Time: 22:36
 */
namespace Jungle\FileSystem\Model {

	/**
	 * Class FileMod
	 * @package Jungle\FileSystem\Model
	 */
	class FileMod{

		const OWNER_READ       = 0400;
		const OWNER_WRITE      = 0200;
		const OWNER_EXECUTE    = 0100;

		const GROUP_READ       = 040;
		const GROUP_WRITE      = 020;
		const GROUP_EXECUTE    = 010;

		const WORLD_READ       = 04;
		const WORLD_WRITE      = 02;
		const WORLD_EXECUTE    = 01;

		/** @var int decimal file mod */
		protected $decimal;


		/** @return bool */
		public function hasOwnerRead(){return boolval($this->decimal & self::OWNER_READ);}

		/** @return bool */
		public function hasOwnerWrite(){return boolval($this->decimal & self::OWNER_WRITE);}

		/** @return bool */
		public function hasOwnerExecute(){return boolval($this->decimal & self::OWNER_EXECUTE);}

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setOwnerRead($readable = true){
			$this->decimal = $readable? $this->decimal | self::OWNER_READ : $this->decimal ^ self::OWNER_READ ;
			return $this;
		}

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setOwnerWrite($writable = true){
			$this->decimal = $writable? $this->decimal | self::OWNER_WRITE : $this->decimal ^ self::OWNER_WRITE ;
			return $this;
		}

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setOwnerExecute($executable = true){
			$this->decimal = $executable? $this->decimal | self::OWNER_EXECUTE : $this->decimal ^ self::OWNER_EXECUTE ;
			return $this;
		}


		/** @return bool */
		public function hasGroupRead(){return boolval($this->decimal & self::GROUP_READ);}

		/** @return bool */
		public function hasGroupWrite(){return boolval($this->decimal & self::GROUP_WRITE);}

		/** @return bool */
		public function hasGroupExecute(){return boolval($this->decimal & self::GROUP_EXECUTE);}

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setGroupRead($readable = true){
			$this->decimal = $readable? $this->decimal | self::GROUP_READ : $this->decimal ^ self::GROUP_READ ;
			return $this;
		}

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setGroupWrite($writable = true){
			$this->decimal = $writable? $this->decimal | self::GROUP_WRITE : $this->decimal ^ self::GROUP_WRITE ;
			return $this;
		}

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setGroupExecute($executable = true){
			$this->decimal = $executable? $this->decimal | self::GROUP_EXECUTE : $this->decimal ^ self::GROUP_EXECUTE ;
			return $this;
		}





		/** @return bool */
		public function hasWorldRead(){return boolval($this->decimal & self::WORLD_READ);}

		/** @return bool */
		public function hasWorldWrite(){return boolval($this->decimal & self::WORLD_WRITE);}

		/** @return bool */
		public function hasWorldExecute(){return boolval($this->decimal & self::WORLD_EXECUTE);}

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setWorldRead($readable = true){
			$this->decimal = $readable? $this->decimal | self::WORLD_READ : $this->decimal ^ self::WORLD_READ ;
			return $this;
		}

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setWorldWrite($writable = true){
			$this->decimal = $writable? $this->decimal | self::WORLD_WRITE : $this->decimal ^ self::WORLD_WRITE ;
			return $this;
		}

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setWorldExecute($executable = true){
			$this->decimal = $executable? $this->decimal | self::WORLD_EXECUTE : $this->decimal ^ self::WORLD_EXECUTE ;
			return $this;
		}




		/**
		 * @return string
		 */
		public function getOctal(){
			return decoct($this->decimal);
		}

		/**
		 * @param string|int $permissions
		 * @return bool
		 */
		public function check($permissions){
			return boolval( $this->decimal & (is_string($permissions)?octdec($permissions):$permissions) );
		}



		/**
		 * @param $permissions
		 * @return $this
		 */
		public function setMod($permissions){
			$this->decimal = $this->decimal | (is_string($permissions)?octdec($permissions):$permissions);
			return $this;
		}

		/**
		 * @param bool $decimal
		 * @return int
		 */
		public function getMod($decimal = true){
			return $decimal?$this->decimal:decoct($this->decimal);
		}



	}
}

