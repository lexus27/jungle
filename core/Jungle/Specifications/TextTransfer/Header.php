<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.01.2016
 * Time: 22:33
 */
namespace Jungle\Specifications\TextTransfer {

	use Jungle\Smart\Keyword\Keyword;
	use Jungle\Specifications\TextTransfer\Header\Value;

	/**
	 * Class Header
	 * @package Jungle\HeaderCover
	 */
	class Header extends Keyword{

		/**
		 * @var int
		 */
		protected $prepareTurn = 1000;

		/**
		 * @var int
		 */
		protected $recoverTurn = 1000;

		/**
		 * @param string $identifier
		 */
		public function setIdentifier($identifier){
			parent::setIdentifier($this->normalize($identifier));
		}

		/**
		 * @param string $name
		 * @return string
		 */
		public static function normalize($name){
			$name = preg_replace('@[\-_ ]+@',' ',$name);
			$name = str_replace(' ','-',ucwords($name));
			return $name;
		}

		/**
		 * @param $header
		 * @return bool|string
		 */
		public static function recognizeHeaderRow($header){
			if(preg_match('@^(\w+[\w\-]+\w+):?(.*)@',$header,$m)){
				return [self::normalize($m[1]),Value::normalize($m[2])];
			}
			return false;
		}


		/**
		 * @param Value[] $values
		 * @param $body
		 */
		public function onDocumentPrepareBody(array $values, &$body){

		}

		/**
		 * @param Value[] $values
		 * @param $body
		 */
		public function onDocumentRecoverBody(array $values,&$body){

		}

		/**
		 *
		 */
		public function getPrepareTurn(){
			return $this->prepareTurn;
		}

		/**
		 *
		 */
		public function getRecoverTurn(){
			return $this->recoverTurn;
		}

	}



}

