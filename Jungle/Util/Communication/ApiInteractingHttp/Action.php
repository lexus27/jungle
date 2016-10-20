<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 21:23
 */
namespace Jungle\Util\Communication\ApiInteractingHttp {

	use Jungle\Util\Communication\ApiInteracting\ActionInterface;
	use Jungle\Util\Communication\ApiInteracting\Combination as MainCombination;
	use Jungle\Util\Communication\HttpClient\Agent;
	use Jungle\Util\Communication\HttpClient\Request;

	/**
	 * Class Action
	 * @package Jungle\Util\Communication\ApiInteractingHttp
	 */
	class Action implements ActionInterface{

		/** @var  array|callable */
		protected $definition;

		/** @var  callable|null */
		protected $validator;

		/** @var  Api */
		public $api;

		/** @var  Combination */
		public $combination;

		/** @var  Process */
		public $process;

		/**
		 * Action constructor.
		 * @param Api $api
		 * @param $definition
		 * @param callable|null $validator
		 */
		public function __construct(Api $api, $definition, callable $validator = null){
			if(!is_array($definition) || !is_callable($definition)){
				throw new \InvalidArgumentException('$definition must be array represent request or callable request evaluator');
			}
			$this->definition = $definition;
			$this->api = $api;
		}

		/**
		 * @param array $params
		 * @param MainCombination $combination
		 * @return mixed
		 */
		public function execute(array $params, MainCombination $combination){
			$this->combination = $combination;
			try{
				if(!$combination instanceof Combination){
					throw new \InvalidArgumentException('Argument $combination must be instance of stream specify Combination');
				}
				$process = $this->process = $this->createProcess();
				$agent = $combination->getAgent();
				if($this->definition){
					$request = $this->resolveRequest($agent);
					$process->setRequest($request);
					$response = $this->api->executeRequest($agent, $request);
					$process->setResult($response);
					$combination->getCollector()->push($process);
					$this->check();
				}
			}finally{
				$this->process = null;
				$this->combination = null;
			}
		}

		/**
		 * @return Process
		 */
		protected function createProcess(){
			return new Process($this);
		}

		/**
		 * @param Agent $agent
		 * @return Request
		 */
		protected function resolveRequest(Agent $agent){

			if(is_callable($this->definition)){
				return call_user_func($this->definition, $this);
			}

			$definition = $this->definition;
			$definition = array_replace([
				'method'        => null,
				'url'           => null,
				'path'          => null,
				'query'         => null,
				'fragment'      => null,
				'post'          => null,
				'files'         => null,
				'headers'       => null,
				'body'          => null,
			],$definition);

			$request = new Request($agent);
			if($definition['method']){
				$request->setMethod($this->_parsePlaceholders($definition['method']));
			}else{
				$request->setMethod('get');
			}
			if($definition['headers']){
				if(is_array($definition['headers'])){
					foreach($definition['headers'] as $key => & $value){
						if($value !== null){
							$value = $this->_parsePlaceholders($value);
						}
					}
					$request->setFiles($definition['headers']);
				}else{
					$request->setFiles($this->_parsePlaceholders($definition['headers']));
				}
			}
			if($definition['url']){
				if(is_array($definition['url'])){
					foreach($definition['url'] as $key => & $value){
						if($value !== null){
							$value = $this->_parsePlaceholders($value);
						}
					}
					$request->setUrl($definition['url']);
				}else{
					$request->setUrl($this->_parsePlaceholders($definition['method']));
				}
			}
			if($definition['path']){
				$request->setUri($this->_parsePlaceholders($definition['path']));
			}
			if($definition['query']){
				if(is_array($definition['query'])){
					foreach($definition['query'] as $key => & $value){
						if($value !== null){
							$value = $this->_parsePlaceholders($value);
						}
					}
					$request->setQueryParams($definition['query']);
				}else{
					$request->setQueryParams($this->_parsePlaceholders($definition['query']));
				}
			}
			if($definition['post']){
				if(is_array($definition['post'])){
					foreach($definition['post'] as $key => & $value){
						if($value !== null){
							$value = $this->_parsePlaceholders($value);
						}
					}
					$request->setPostParams($definition['post']);
				}else{
					$request->setPostParams($this->_parsePlaceholders($definition['post']));
				}
			}
			if($definition['files']){
				if(is_array($definition['files'])){
					foreach($definition['files'] as $key => & $value){
						if($value !== null){
							$value = $this->_parsePlaceholders($value);
						}
					}
					$request->setFiles($definition['files']);
				}else{
					$request->setFiles($this->_parsePlaceholders($definition['files']));
				}
			}
			if($definition['body']){
				$request->setContent($this->_parsePlaceholders($definition['body']));
			}

			return $request;
		}

		/**
		 * @param $value
		 * @return array|mixed|string
		 */
		protected function _parsePlaceholders($value){
			if(is_callable($value)){
				return call_user_func($value, $this);
			}elseif(is_string($value)){
				if(substr($value,0,2) === '{{$' && substr($value,-3) === '$}}'){
					return $this->_takeValue(substr($value,2,-3));
				}else{
					$replacer = $this->api->getReplacer();
					return $replacer->replace($value,function($placeholder){
						return $this->_takeValue($placeholder);
					});
				}
			}else{
				return $value;
			}
		}

		/**
		 * @param $query
		 * @param null $source
		 * @param null $query_full
		 * @return array
		 */
		protected function _takeValue($query, $source = null, $query_full = null){
			if($source === null) $source = $this->process;
			if($query_full === null)$query_full = $query;
			if(!is_array($query)) $query = array_filter(explode('.', $query),null, FILTER_FLAG_EMPTY_STRING_NULL);

			$slug = array_shift($query);

			if(is_object($source)){
				if(!isset($source->{$slug})){
					throw new \InvalidArgumentException("Action {$this->combination->key()} require param {$query_full}");
				}else{
					if($query){
						return $this->_takeValue($query, $source->{$slug}, $query_full);
					}else{
						return $source->{$slug};
					}
				}
			}elseif(is_array($source)){
				if(!isset($source[$slug])){
					throw new \InvalidArgumentException("Action {$this->combination->key()} require param {$query_full}");
				}else{
					if($query){
						return $this->_takeValue($query, $source[$slug], $query_full);
					}else{
						return $source->{$slug};
					}
				}
			}else{
				throw new \InvalidArgumentException("Action {$this->combination->key()} require param {$query_full}");
			}
		}

		/**
		 *
		 */
		protected function check(){
			$this->api->validateProcess($this->process);
			if(is_callable($this->validator)){
				call_user_func($this->validator, $this);
			}
		}




	}
}

