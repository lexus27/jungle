<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.01.2016
 * Time: 15:58
 */
namespace Jungle\Specifications\TextTransfer {

	use Jungle\Basic\Collection\ObjectStorage;
	use Jungle\Specifications\TextTransfer\Header\Pool;
	use Jungle\Specifications\TextTransfer\Header\Value;

	/**
	 * Class Document
	 * @package Jungle\HeaderCover
	 */
	class Document{

		/** @var ObjectStorage|Header[] */
		protected $headers = [];

		/** @var IBody|string */
		protected $body;

		/** @var callable|null */
		protected $recoverSorter;

		/** @var callable|null */
		protected $prepareSorter;


		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setHeader($key,$value){

			$manager = Pool::getDefault();
			$header = $manager->get($key);

			if(!$this->headers){
				$this->headers = new ObjectStorage();
			}

			if(!$this->headers->has($header)){
				$this->headers->set($header,[]);
			}
			/** @var Value[] $h */
			$h = & $this->headers->get($header);
			foreach($h as $v){
				if($v->equal($value)){
					return $this;
				}
			}

			if(!$value instanceof Value){
				$v = new Value();
				$recognized = $v->recognize($value);
				if($recognized){
					$v->fromArray($recognized);
				}
				$h[] = $v;
			}else{
				$h[] = $value;
			}

			return $this;
		}

		/**
		 * @param $key
		 * @return Value|Value[]|null
		 */
		public function & getHeader($key){
			$a = null;
			if($this->headers){
				$header = Pool::getDefault()->get($key);
				if($this->headers->has($header)){
					$h = & $this->headers->get($header);
					if(count($h)===1){
						return $h[0];
					}else{
						return $h;
					}
				}
			}
			return $a;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasHeader($key){
			if($this->headers){
				$header = Pool::getDefault()->get($key);
				return $this->headers->has($header);
			}
			return false;
		}

		/**
		 * @param $key
		 * @return int
		 */
		public function countHeader($key){
			if($this->headers){
				$header = Pool::getDefault()->get($key);
				if(!$this->headers->has($header)){
					return 0;
				}
				$v = $this->headers->get($header);
				return count($v);
			}
			return 0;
		}


		/**
		 * @param $key
		 * @return mixed
		 */
		protected function normalizeHeaderKey(Header $key){
			return "{$key}";
		}

		protected function normalizeHeaderValue($key,Value $value){
			return "{$value}";
		}

		/**
		 *
		 */
		protected function headerString(){
			$header = '';
			if($this->headers){
				foreach($this->headers as $key){
					$values = $this->headers->get($key);
					$key = $this->normalizeHeaderKey($key);



					foreach($values as $value){
						$value = $this->normalizeHeaderValue($key,$value);
						$header.=$key.': '.$value."\r\n";
					}
				}
			}
			return $header;
		}


		/**
		 * @param IBody|string $body
		 * @return $this
		 */
		public function setBody($body=null){
			$old = $this->body;
			if($old!==$body){
				$this->body = $body;
				if($old instanceof IBody){
					$old->setDocument(null);
				}
				if($body instanceof IBody){
					$body->setDocument($this);
				}
			}
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getBody(){
			return $this->body;
		}

		/**
		 * @param bool $recover
		 * @return array
		 */
		protected function getHeaderSortRules($recover = false){
			$rules = [];
			foreach($this->headers as $header){
				$rules[$header->getIdentifier()] = $recover?$header->getRecoverTurn():$header->getPrepareTurn();
			}
			return $rules;
		}

		/**
		 * @param bool|false $recover
		 * @return \Closure
		 */
		protected function getHeaderSorter($recover = false){
			if($recover){

				if(!$this->recoverSorter){
					/**
					 * @param Header $header1
					 * @param Header $header2
					 * @return int
					 */
					$this->recoverSorter = function(Header $header1, Header $header2){
						$rules = $this->getHeaderSortRules();
						$id1 = $header1->getIdentifier();
						$id2 = $header2->getIdentifier();
						if($rules[$id1]>$rules[$id2]){
							return 1;
						}else if($rules[$id1]<$rules[$id2]){
							return -1;
						}
						return 0;
					};
				}


			}else{

				if(!$this->prepareSorter){
					/**
					 * @param Header $header1
					 * @param Header $header2
					 * @return int
					 */
					$this->prepareSorter = function(Header $header1, Header $header2){
						$rules = $this->getHeaderSortRules();
						$id1 = $header1->getIdentifier();
						$id2 = $header2->getIdentifier();
						if($rules[$id1]>$rules[$id2]){
							return 1;
						}else if($rules[$id1]<$rules[$id2]){
							return -1;
						}
						return 0;
					};
				}


			}

			return $recover?$this->recoverSorter:$this->prepareSorter;
		}

		/**
		 * @param $body
		 * @return string
		 */
		protected function prepareBody($body){
			if($this->headers){
				/** @var Header[] $h */
				$h = $this->headers->getAllObjects();
				usort($h,$this->getHeaderSorter(false));
				foreach($h as $key){
					$values = $this->headers->get($key);
					$key->onDocumentPrepareBody($values,$body);
				}
			}
			return $body;
		}

		/**
		 * @return string
		 */
		public function represent(){
			$body = strval($this->getBody());
			return $this->headerString()."\r\n".$this->prepareBody($body);
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->represent();
		}


		/**
		 * @param $raw
		 * @return array|Document
		 */
		public static function getDocument($raw){
			if($raw instanceof Document){
				return $raw;
			}
			$document = preg_split("@\r\n\r\n@",$raw,2);
			$header = $document[0];
			$header = preg_split("@(?<!;\s)\n@",$header);
			$body = $document[1];
			$document = new Document();
			foreach($header as $v){
				list($k,$v) = explode(': ',$v);
				if($k && $v){
					$document->setHeader($k,preg_replace("@\n@",'',$v));
				}
			}
			if($document->headers){
				/** @var Header[] $h */
				$h = $document->headers->getAllObjects();
				usort($h,$document->getHeaderSorter(true));
				foreach($h as $key){
					$values = $document->headers->get($key);
					$key->onDocumentRecoverBody($values,$body);
				}
			}
			$document->setBody($body);
			return $document;
		}

		/**
		 * @param array $headers
		 * @param null $modeExists
		 *              TRUE    - Выставить значения только в тех Заголовках которые присутствуют
		 *              FALSE   - Выставить значения только в тех Заголовках которые отсутствуют
		 *              NULL    - Строгий , обычный режим без отбора
		 * @param bool $existsCheckEmpty - режим отбора производится проверкой на присутствующее Значение, а не заголовок
		 *              FALSE   - Проверка на присутствие самого заголовка в документе
		 *              TRUE    - Проверка на присутствие значения заголовка, не важно есть сам заголовок или нету
		 * @return $this
		 */
		public function setHeaders(array $headers, $modeExists = null, $existsCheckEmpty = false){

			if($modeExists === false){
				foreach($headers as $header => $value){
					if((!$existsCheckEmpty && !$this->hasHeader($header)) || ($existsCheckEmpty && !$this->getHeader($header))){
						$this->setHeader($header,$value);
					}
				}
			}else if($modeExists === true){
				foreach($headers as $header => $value){
					if((!$existsCheckEmpty && $this->hasHeader($header)) || ($existsCheckEmpty && $this->getHeader($header))){
						$this->setHeader($header,$value);
					}
				}
			}else{
				foreach($headers as $header => $value){
					$this->setHeader($header,$value);
				}
			}
			return $this;
		}

	}
}

