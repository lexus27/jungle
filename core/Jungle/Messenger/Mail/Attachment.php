<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:49
 */
namespace Jungle\Messenger\Mail {

	/**
	 * Class Attachment
	 * @package Jungle\Messenger\Mail
	 */
	class Attachment implements IAttachment{

		/**
		 * @var string
		 */
		protected $type = self::TYPE_DEFAULT;

		/**
		 * @var string
		 */
		protected $disposition = self::DISPOSITION_DEFAULT;

		/**
		 * @var string
		 */
		protected $raw;

		/**
		 * @var string
		 */
		protected $src;

		/**
		 * @var string|null
		 */
		protected $name;

		/**
		 * @var array
		 */
		protected $headers = [];

		/**
		 * @param $disposition
		 * @return $this
		 */
		public function setDisposition($disposition = self::DISPOSITION_DEFAULT){
			$this->disposition = $disposition;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDisposition(){
			return $this->disposition;
		}

		/**
		 * @param $type
		 * @return $this
		 * Mime-Type
		 */
		public function setType($type = self::TYPE_DEFAULT){
			$this->type = $type;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param $raw
		 * @return $this
		 */
		public function setRaw($raw){
			$this->raw = $raw;
			$this->src = null;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getRaw(){
			if($this->src && !$this->raw && $this->raw!==false){
				if(!is_readable($this->src)){
					throw new \LogicException('File is not valid parent_src "' . $this->src . '"');
				}
				$this->raw = file_get_contents($this->src);
				if(!$this->raw) $this->raw = false;
			}
			return $this->raw===false?null:$this->raw;
		}

		/**
		 * @param $src
		 * @return $this
		 */
		public function setSrc($src){
			$this->src = $src;
			$this->raw = null;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getSrc(){
			return $this->src;
		}

		/**
		 * @param null $name
		 * @return $this
		 */
		public function setName($name = null){
			$this->name = $name;
		}

		/**
		 * @return string|null
		 */
		public function getName(){
			return $this->name?:($this->src?basename($this->src):null);
		}

		/**
		 * @param array $headers
		 * @return $this
		 */
		public function setHeaders(array $headers){
			$this->headers = $headers;
			return $this;
		}

		/**
		 *
		 */
		public function getHeaders(){
			return $this->headers;
		}


	}
}

