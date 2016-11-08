<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 22:59
 */
namespace Jungle\Util\Communication\HttpClient\CacheManager {

	/**
	 * Class Record
	 * @package Jungle\Util\Communication\HttpClient\CacheManager\CacheManager
	 */
	class CacheEntry{

		/** @var array  */
		protected $defaults = [];

		public $time;

		public $max_age;

		public $expired;

		public $tag;

		public $last_modified;

		public $must_revalidate = false;

		public $no_cache = true;

		public $private = false;

		public $response;

		/**
		 * Record constructor.
		 * @param array $data
		 */
		public function __construct(array $data = []){
			$properties = get_object_vars($this);
			unset($properties['default']);
			$this->defaults = array_intersect_key($data, $properties);
			foreach($this->defaults as $k => $v){
				$this->{$k} = $v;
			}
		}

		/**
		 * @return $this
		 */
		public function apply(){
			foreach($this->defaults as $k => &$v){
				$v = $this->{$k};
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function toArray(){
			$a = [];
			foreach($this->defaults as $k => $v){
				$a[$k] = $this->{$k};
			}
			return $a;
		}

		/**
		 * @return bool
		 */
		public function isDirty(){
			foreach($this->defaults as $k => $v){
				if($this->{$k} !== $v){
					return true;
				}
			}
			return false;
		}

	}
}

