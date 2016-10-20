<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 21:58
 */
namespace Jungle\Util\Communication\HttpClient\CacheManager {

	use Jungle\Util\Communication\HttpClient\Request;

	/**
	 * Class Resource
	 * @package Jungle\Util\Communication\HttpClient\CacheManager\CacheManager
	 */
	class CacheResource implements CacheResourceInterface{

		/** @var array  */
		protected $defaults = [];

		/** @var  array */
		protected $vary = [];

		/** @var  CacheEntry[] */
		protected $entries = [];


		/**
		 * Resource constructor.
		 */
		public function __construct(){
			$this->defaults['vary'] = $this->vary;
		}

		/**
		 * @param array $vary
		 * @return $this
		 */
		public function setVary(array $vary = null){
			$vary = $vary?:[];
			if(($old = $this->vary)!==$vary){
				$this->vary = $vary;
				$this->defaults['vary'] = $old;
			}
			return $this;
		}

		/**
		 * @param Request $request
		 * @return string
		 */
		public function makeSignature(Request $request){
			if($this->vary){
				$a = [];
				foreach($this->vary as $name){
					$a[] = implode(', ',$request->getHeaderCollection($name));
				}
				$a = implode("\r\n", $a);
				if($a){
					return md5($a);
				}
			}
			return '';
		}

		/**
		 * @return CacheEntry[]
		 */
		public function getEntries(){
			return $this->entries;
		}

		/**
		 * @param string|null $vary_signature
		 * @return CacheEntry
		 */
		public function getEntry($vary_signature){
			if(!$vary_signature)$vary_signature = '';
			if(isset($this->entries[$vary_signature])){
				$entry = $this->entries[$vary_signature];
				return $entry;
			}
			return null;
		}

		/**
		 * @return CacheEntry
		 */
		public function createEntry(){
			return new CacheEntry();
		}

		/**
		 * @param $vary_signature
		 * @param CacheEntry $entry
		 * @return $this
		 */
		public function updateEntry($vary_signature, CacheEntry $entry){
			if(!$vary_signature)$vary_signature = '';
			if(!isset($this->entries[$vary_signature])){
				$this->entries[$vary_signature] = $entry;
			}
			$entry->apply();
			return $this;
		}

		/**
		 * @param $vary_signature
		 * @return $this
		 */
		public function removeEntry($vary_signature){
			if(!$vary_signature)$vary_signature = '';
			if(isset($this->entries[$vary_signature])){
				unset($this->entries[$vary_signature]);
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isDirty(){
			foreach($this->defaults as $k => $v){
				if($this->{$k}!==$v){
					return true;
				}
			}
			foreach($this->entries as $entry){
				if($entry->isDirty()){
					return true;
				}
			}
			return false;
		}


	}
}

