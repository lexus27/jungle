<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 21:58
 */
namespace Jungle\Util\Communication\Http\CacheManager {

	use Jungle\Util\Communication\Http\Request;

	/**
	 * Class Resource
	 * @package Jungle\Util\Communication\Http\CacheManager
	 */
	class Resource{


		/** @var  string */
		protected $url;

		/** @var  array */
		protected $vary = [];

		/** @var   Record[] */
		protected $records = [];


		/**
		 * @param array $vary
		 * @return $this
		 */
		public function setVary(array $vary = null){
			$this->vary = $vary?:null;
			return $this;
		}

		/**
		 * @param $url
		 * @return $this
		 */
		public function setUrl($url){
			$this->url = $url;
			return $this;
		}


		/**
		 * @param Request $request
		 * @return string|null
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
			return null;
		}


		/**
		 * @param string|null $vary_signature
		 * @return Record|null
		 */
		public function getRecord($vary_signature){
			if(!$vary_signature)$vary_signature = '';
			if(isset($this->records[$vary_signature])){
				$resource = $this->records[$vary_signature];
				return $resource;
			}
			return null;
		}

		/**
		 * @param string|null $vary_signature
		 * @param Record $record
		 */
		public function setRecord($vary_signature, Record $record){
			if(!$vary_signature)$vary_signature = '';
			$this->records[$vary_signature] = $record;
		}

		public function getRecords(){
			return $this->records;
		}

	}
}

