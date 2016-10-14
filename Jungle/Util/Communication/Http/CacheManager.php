<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 18:42
 */
namespace Jungle\Util\Communication\Http {

	use Jungle\Util\Communication\Http\CacheManager\Record;
	use Jungle\Util\Communication\Http\CacheManager\Resource;
	use Jungle\Util\Specifications\Hypertext\Document\Exception\EarlyException;
	use Jungle\Util\Specifications\Hypertext\Header;

	/**
	 * Class CacheManager
	 * @package Jungle\Util\Communication\Http
	 */
	class CacheManager{

		/** @var  CacheManager\Resource[] */
		protected $resources = [];

		/** @var bool  */
		protected $active = false;

		/**
		 * @param bool|true $active
		 * @return $this
		 */
		public function setActive($active = true){
			$this->active = $active;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isActive(){
			return $this->active;
		}

		/**
		 *
		 */
		public function clear(){
			$this->resources = [];
		}

		/**
		 * @param Request $request
		 * @param array $vary
		 * @return string
		 */
		public function getCacheKey(Request $request, array $vary = null){
			$url = $request->getUrl();
			if($vary!==null){
				$vary_values = [];
				foreach($vary as $name){
					$vary_values[] = $name.': '.implode(', ',$request->getHeaderCollection($vary));
				}
				$vary_values = implode("\r\n");
				return md5($url . ($vary_values?"\r\n".$vary_values:''));
			}
			return md5($url);
		}


		/**
		 * @param $maxAge
		 * @param $requestTime
		 * @param null $now
		 * @return bool
		 */
		public function maxAgeIsOverdue($maxAge, $requestTime, $now = null){
			if($now===null)$now = time();
			return $now > $requestTime + $maxAge;
		}

		/**
		 * @param $expires
		 * @param null $now
		 * @return bool
		 */
		public function expiresIsOverdue($expires, $now = null){
			$expires = strtotime($expires);
			if($now===null)$now = time();
			return $now > $expires;
		}

		/**
		 * @param Request $request
		 * @throws EarlyException
		 */
		public function checkRequest(Request $request){
			$url = $request->getUrl();
			$resource = $this->_loadResource($url);
			if($resource!==null){
				$signature = $resource->makeSignature($request);
				$request->setOption('cache_manager::signature', $signature);
				$record = $resource->getRecord($signature);
				if($record){
					if(!$record->no_cache){
						if($record->must_revalidate){

							if(isset($record->max_age) && !$this->maxAgeIsOverdue($record->max_age, $record->time)){
								$request->setOption('cache_manager::from_cache', true);
								$response = $request->getResponse();
								$response->reuse($record->response);
							}

							if(isset($record->expired) && !isset($record->max_age) && !$this->expiresIsOverdue($record->expired, $record->time)){
								$request->setOption('cache_manager::from_cache', true);
								$response = $request->getResponse();
								$response->reuse($record->response);
							}

						}else{

							if(isset($record->max_age)){
								if(!$this->maxAgeIsOverdue($record->max_age, $record->time)){
									$request->setOption('cache_manager::from_cache', true);
									$response = $request->getResponse();
									$response->reuse($record->response);
									throw new EarlyException();
								}
							}

							if(isset($record->expired)){
								if(!$this->expiresIsOverdue($record->expired, $record->time)){
									$request->setOption('cache_manager::from_cache', true);
									$response = $request->getResponse();
									$response->reuse($record->response);
									throw new EarlyException();
								}
							}
						}
					}

					if(isset($record->tag)){
						$request->setOption('cache_manager::validate', true);
						$request->setOption('cache_manager::validate-ETag', $record->tag);
						$request->setHeader('If-None-Match', '"'.$record->tag.'"');
					}

					if(isset($record->last_modified)){
						$request->setOption('cache_manager::validate', true);
						$request->setOption('cache_manager::validate-LastModified', $record->last_modified);
						$request->setHeader('If-Modified-Since', $record->last_modified);
					}
				}
			}
		}

		/**
		 * @param Response $response
		 * @param Request $request
		 * @throws EarlyException
		 */
		public function onResponseHeaders(Response $response, Request $request){
			if($response->getCode() === 304){
				$url = $request->getUrl();
				$signature = false;
				$resource = null;
				$record = null;
				if(isset($this->resources[$url])){
					$resource = $this->resources[$url];
					$signature = $request->getOption('cache_manager::signature');
					if($signature!==false){
						if( ($record = $resource->getRecord($signature)) ){
							if($request->getOption('cache_manager::validate')){
								$response->reuse($record->response, true);
							}
						}
					}
				}
				$this->_updateRecord($url,$request,$response,$signature,$resource,$record);
				if($response->isReused()){
					throw new EarlyException();
				}
			}
		}

		/**
		 * @param Response $response
		 * @param Request $request
		 */
		public function onResponse(Response $response, Request $request){
			if($response->getCode()!==304){
				$url = $request->getUrl();
				$signature = false;
				$resource = null;
				$record = null;
				$resource = $this->_loadResource($url);
				if($resource!==null){
					$signature = $request->getOption('cache_manager::signature', false);
					if($signature!==false){
						if( ($record = $resource->getRecord($signature)) ){
							if($request->getOption('cache_manager::validation')){
								if($response->getCode() === 304){
									$response->reuse($record->response, true);
								}
							}
						}
					}
				}
				$this->_updateRecord($url,$request,$response,$signature,$resource,$record);
			}
		}


		/**
		 * @param $url
		 * @param Request $request
		 * @param Response $response
		 * @param bool $signature
		 * @param CacheManager\Resource $resource
		 * @param Record $record
		 */
		protected function _updateRecord($url, Request $request, Response $response, $signature = false, Resource $resource = null, Record $record = null){
			$request_time = $request->getTime();

			$allowed = true;
			$tokens = [];
			$max_age = null;
			if(($cache_control = $response->getHeader('Cache-Control'))){
				$cache_control = $this->decompositeHeaderValue($cache_control);

				if($cache_control){
					$params = [];
					foreach($cache_control as $i => $element){
						foreach($element as $k => $v){
							if(is_int($k)){
								$tokens[] = strtolower($v);
								if($v === 'no-store'){
									$allowed = false;
								}
							}else{
								$params[$k] = $v;
							}
						}
					}
				}
				$max_age = isset($params['max-age'])?$params['max-age']:null;
			}

			if($allowed){
				$a = [];
				if($max_age!==null){
					$a['max_age'] = intval($max_age);
				}
				if(($expired = $response->getHeader('Expired'))){
					$a['expired'] = $expired;
				}
				if(($eTag = $response->getHeader('ETag'))){
					$a['tag'] = trim($eTag,'\'"');
				}
				if(($last_modified = $response->getHeader('Last-Modified'))){
					$a['last_modified'] = $last_modified;
				}


				if(isset($a) && !empty($a)){
					if(($vary = $response->getHeader('Vary'))){
						$vary = explode(',', $vary);
						foreach($vary as & $item){
							$item = trim($item);
							$item = Header::normalize($item);
						}
					}else{
						$vary = null;
					}

					$created_resource = false;
					if(!$resource){
						$created_resource = true;
						$resource = new Resource();
					}
					$resource->setVary($vary);
					if($signature===false){
						$signature = $resource->makeSignature($request);
					}

					if(!$record){
						$record = new Record();
						$record->time = $request_time;
						$record->response = $response;
					}

					foreach($tokens as $itm){
						if($itm === 'public'){
							$record->private = false;
						}elseif($itm === 'private'){
							$record->private = true;
						}elseif(property_exists($record,$itm)){
							$record->{$itm} = true;
						}
					}
					foreach($a as $k => $v) $record->{$k} = $v;

					$resource->setRecord($signature, $record);

					if($created_resource){
						$this->_setResource($url, $resource);
					}
				}
			}
		}

		/**
		 * @param $value
		 * @return null
		 */
		public function decompositeHeaderValue($value){
			if($value){
				$elements = [];
				$value = explode(',',trim($value," \r\n,"));
				if($value){
					foreach($value as $element){
						$element = trim($element);
						if($element){
							$element = explode(';',$element);
							$a = [];
							foreach($element as $item){
								$item = trim($item);
								if(stripos($item,'=')!==false){
									list($key,$value) = array_replace([null,null], explode('=',$item));
									$a[trim($key)] = trim($value);
								}else{
									$a[] = $item;
								}
							}
							$elements[] = $a;
						}
					}
				}
				return $elements;
			}else{
				return null;
			}
		}

		/**
		 * @param $url
		 * @return CacheManager\Resource|null
		 */
		protected function _loadResource($url){
			return isset($this->resources[$url])?$this->resources[$url]:null;
		}

		/**
		 * @param $url
		 * @param CacheManager\Resource $resource
		 */
		protected function _setResource($url, CacheManager\Resource $resource){
			$this->resources[$url] = $resource;
		}

	}
}

