<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.01.2017
 * Time: 21:41
 */
namespace Jungle\Data\Record\Locator {

	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class Path
	 * @package Jungle\Data\Record\Locator
	 */
	class Path extends Property{

		/** @var  string|null */
		public $extra;

		public $data = [];

		/**
		 * Path constructor.
		 * @param Schema $opening_schema
		 * @param $path
		 * @param null $field
		 * @param null $extra
		 * @param Point|null $point
		 */
		public function __construct(Schema $opening_schema, $path, $field = null, $extra = null,Point $point = null){
			$this->opening_schema = $opening_schema;
			$this->path = $path;
			$this->field = $field;
			$this->extra = $extra;
			$this->point = $point;
		}

		/**
		 * @param $default_path
		 * @return null|string
		 */
		public function getPrevPath($default_path = null){
			return $this->point && $this->point->prev?$this->point->prev->path:$default_path;
		}

		/**
		 * @param array|null $data
		 * @param bool|true $merge
		 * @return $this|array
		 */
		public function data(array $data = null, $merge = true){
			if($data === null){
				return $this->data;
			}else{
				$this->data = $merge?array_replace($this->data,$data):$data;
				return $this;
			}
		}

		public function __set($p,$v){
			$this->data[$p] = $v;
		}
		public function __get($p){
			return isset($this->data[$p])?$this->data[$p]:null;
		}
		public function __isset($p){
			return array_key_exists($p,$this->data);
		}
		public function __unset($p){
			unset($this->data[$p]);
		}
	}
}

