<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.01.2016
 * Time: 16:18
 */
namespace Jungle\Specifications\TextTransfer\Body {

	use Jungle\Specifications\TextTransfer\Document;
	use Jungle\Specifications\TextTransfer\IBody;

	/**
	 * Class PartitionCollection
	 * @package Jungle\HeaderCover
	 */
	class Multipart implements IBody{

		/**
		 * @var Document
		 */
		protected $document;

		/**
		 * @param Document $document
		 * @return $this
		 */
		public function setDocument(Document $document=null){
			$old = $this->document;
			if($old!==$document){
				$this->document = $document;
				if($old){
					$old->setBody(null);
				}
				if($document){
					$document->setBody($this);
				}

			}
			return $this;
		}

		public function getDocument(){
			return $this->document;
		}

		/**
		 * @var Document[]
		 */
		protected $partitions = [];

		/**
		 * @var string
		 */
		protected $boundary;

		/**
		 * @param Document $document
		 * @return $this
		 */
		public function addPart(Document $document){
			if($this->searchPart($document)===false){
				$this->partitions[] = $document;
			}
			return $this;
		}

		/**
		 * @param Document $document
		 * @return bool|int
		 */
		public function searchPart(Document $document){
			return array_search($document,$this->partitions,true);
		}

		/**
		 * @param Document $document
		 * @return $this
		 */
		public function removePart(Document $document){
			if(($i=$this->searchPart($document))!==false){
				array_splice($this->partitions,$i,1);
			}
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getContent(){
			return $this->partitions;
		}

		/**
		 * @return mixed
		 */
		public function getPreparedContent(){

			$body = '';
			if($this->partitions){
				$bound = $this->getBoundary();
				$document = $this->getDocument();
				if($document){
					$this->getDocument()->setHeader('Content-Type','multipart/mixed; boundary="'.$bound.'"');
				}
				$i=0;
				foreach($this->partitions as $partition_document){
					$body.= ($i>0?"\r\n":'')."--{$bound}\r\n";
					$body.=$partition_document->represent();
					$i++;
				}
				if($i>0){
					$body.="\r\n--{$bound}--\r\n";
				}
			}
			return $body;
		}

		/**
		 * @return string
		 */
		public function getBoundary(){
			if(!$this->boundary){
				$this->boundary = uniqid('----=_');
			}
			return $this->boundary;
		}

		/**
		 * @param $boundary
		 * @return $this
		 */
		public function setBoundary($boundary){
			$this->boundary = $boundary;
			return $this;
		}

		/**
		 * @see getPreparedContent
		 * @return mixed
		 */
		public function __toString(){
			return $this->getPreparedContent();
		}

		/**
		 * @param $partitions
		 * @param $boundary
		 * @return Multipart
		 */
		public static function parse($partitions,$boundary){
			$multipart = new Multipart();
			$multipart->setBoundary($boundary);
			$boundary ='--'.$boundary;
			$partitions = explode($boundary,$partitions);
			$partitions = array_filter($partitions, function($v){
				return !preg_match("@^[\n\-\s]+$@",$v);
			});
			foreach($partitions as $part){
				$part = Document::getDocument($part);
				if($part){
					$multipart->addPart($part);
				}
			}
			return $multipart;
		}

		/**
		 * @return mixed
		 */
		public function getRaw(){
			return [
				'partitions' => $this->partitions,
				'boundary' => $this->boundary,
			];
		}

		/**
		 * @param $raw
		 * @return mixed
		 */
		public function setRaw($raw){
			$this->partitions = isset($raw['partitions']) && is_array($raw['partitions'])?$raw['partitions']:$this->partitions;
			$this->boundary = isset($raw['boundary']) && is_array($raw['boundary'])?$raw['boundary']:$this->boundary;
		}

	}
}

