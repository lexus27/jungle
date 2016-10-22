<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 18:42
 */
namespace Jungle\Util\Communication\HttpClient\CacheManager {

	use Jungle\Util\Communication\HttpClient\Request;
	use Jungle\Util\Communication\HttpClient\Response;
	use Jungle\Util\Communication\Hypertext\Document\Exception\ProcessorEarlyException;
	use Jungle\Util\Communication\Hypertext\Header;

	/**
	 * Class CacheManager
	 * @package Jungle\Util\Communication\HttpClient
	 */
	class CacheManager{

		/** @var  CacheResourceInterface[] */
		protected $resources = [];

		/**
		 *
		 */
		public function clear(){
			$this->resources = [];
		}


		/**
		 * @param Request $request
		 * @throws ProcessorEarlyException
		 */
		public function checkRequest(Request $request){
			$url = $request->getUrl();
			$resource = $this->loadResource($url);
			if($resource!==null){
				$signature = $resource->makeSignature($request);
				$request->setOption('cache_manager::signature', $signature);
				$record = $resource->getEntry($signature);
				if($record){
					if(!$record->no_cache){
						if($record->must_revalidate){
							if(isset($record->max_age)){
								if(!$this->maxAgeIsOverdue($record->max_age, $record->time)){
									$request->setOption('cache_manager::from_cache', true);
									$response = $request->getResponse();
									$response->reuse($record->response);
									throw new ProcessorEarlyException();
								}
							}

							if(isset($record->expired) && !isset($record->max_age)){
								if(!$this->expiresIsOverdue($record->expired, $record->time)){
									$request->setOption('cache_manager::from_cache', true);
									$response = $request->getResponse();
									$response->reuse($record->response);
									throw new ProcessorEarlyException();
								}
							}

						}else{

							if(isset($record->max_age)){
								if(!$this->maxAgeIsOverdue($record->max_age, $record->time)){
									$request->setOption('cache_manager::from_cache', true);
									$response = $request->getResponse();
									$response->reuse($record->response);
									throw new ProcessorEarlyException();
								}
							}

							if(isset($record->expired)){
								if(!$this->expiresIsOverdue($record->expired, $record->time)){
									$request->setOption('cache_manager::from_cache', true);
									$response = $request->getResponse();
									$response->reuse($record->response);
									throw new ProcessorEarlyException();
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
		 * @throws ProcessorEarlyException
		 */
		public function onResponseHeaders(Response $response, Request $request){
			if($response->getCode() === 304){
				$signature = $resource = $record = null;
				$url = $request->getUrl();
				$resource = $this->loadResource($url);
				if($resource){
					$signature = $request->getOption('cache_manager::signature');
					if($signature!==null){
						if( ($record = $resource->getEntry($signature)) ){
							if($request->getOption('cache_manager::validate')){
								$response->reuse($record->response, true);
							}
						}
					}
				}
				$this->updateResource($url,$request,$response,$resource,$signature,$record);
				if($response->isReused()){
					throw new ProcessorEarlyException();
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
				$signature = $record = null;
				$resource = $this->loadResource($url);
				if($resource!==null){
					$signature = $request->getOption('cache_manager::signature');
					if($signature!==null){
						if( ($record = $resource->getEntry($signature)) ){
							if($request->getOption('cache_manager::validation')){
								if($response->getCode() === 304){
									$response->reuse($record->response, true);
								}
							}
						}
					}
				}
				$this->updateResource($url,$request,$response,$resource, $signature, $record);
			}
		}


		/**
		 * @param $url
		 * @param Request $request
		 * @param Response $response
		 * @param CacheResourceInterface $resource
		 * @param bool $signature
		 * @param CacheEntry $entry
		 */
		protected function updateResource($url, Request $request, Response $response, CacheResourceInterface $resource = null, $signature = null, CacheEntry $entry = null){
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

					if(is_null($resource)){
						$resource = $this->createResource();
					}

					$resource->setVary($vary);

					if(is_null($signature)){
						$signature = $resource->makeSignature($request);
					}

					if(!$entry){
						$entry = $resource->createEntry();
						$entry->time = $request_time;
						$entry->response = $response;
					}

					foreach($tokens as $itm){
						if($itm === 'public'){
							$entry->private = false;
						}elseif($itm === 'private'){
							$entry->private = true;
						}elseif(property_exists($entry,$itm)){
							$entry->{$itm} = true;
						}
					}

					foreach($a as $k => $v){
						$entry->{$k} = $v;
					}

					$resource->updateEntry($signature, $entry);
					$this->bindResource($url, $resource);
				}
			}
		}

		/**
		 * @return Resource
		 */
		protected function createResource(){
			return new CacheResource();
		}

		/**
		 * @param $value
		 * @return array|null
		 */
		protected function decompositeHeaderValue($value){
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
		 * @param Request $request
		 * @param array $vary
		 * @return string
		 */
		protected function getCacheKey(Request $request, array $vary = null){
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
		protected function maxAgeIsOverdue($maxAge, $requestTime, $now = null){
			if($now===null)$now = time();
			return $now > $requestTime + $maxAge;
		}

		/**
		 * @param $expires
		 * @param null $now
		 * @return bool
		 */
		protected function expiresIsOverdue($expires, $now = null){
			$expires = strtotime($expires);
			if($now===null)$now = time();
			return $now > $expires;
		}

		/**
		 * @param $url
		 * @return CacheResourceInterface|null
		 */
		protected function loadResource($url){
			return isset($this->resources[$url])?$this->resources[$url]:null;
		}

		/**
		 * @param $url
		 * @param CacheResourceInterface $resource
		 */
		protected function bindResource($url, CacheResourceInterface $resource){
			$this->resources[$url] = $resource;
		}

	}
}

