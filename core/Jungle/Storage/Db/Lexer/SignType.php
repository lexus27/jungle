<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 20:49
 */
namespace Jungle\Storage\Db\Lexer {

	use Jungle\Smart\Keyword\Keyword;

	/**
	 * Class SignType
	 * @package Jungle\Storage\Db\Lexer
	 */
	class SignType extends Keyword{

		/**
		 * @param $recognized
		 * @return mixed
		 */
		public function normalize($recognized){
			$fn = $this->getNormalizer();
			if($fn){
				return Sign::callFn($fn,$recognized);
			}else{
				return $recognized;
			}
		}

		/**
		 * @param $normalized
		 * @return mixed
		 */
		public function render($normalized){
			$fn = $this->getRenderer();
			if($fn){
				return Sign::callFn($fn,$normalized);
			}else{
				return $normalized;
			}
		}

		/**
		 * @return SignType
		 */
		public static function getDefaultType(){
			static $defaultType = null;
			if(!$defaultType){
				$defaultType = new SignType();
			}
			return $defaultType;
		}

		/**
		 * @param $normalizer
		 * @return $this
		 */
		public function setNormalizer($normalizer){
			$this->setOption('normalizer',$normalizer);
			return $this;
		}

		/**
		 * @return null
		 */
		public function getNormalizer(){
			return $this->getOption('normalizer');
		}



		/**
		 * @param $renderer
		 * @return $this
		 */
		public function setRenderer($renderer){
			$this->setOption('renderer',$renderer);
			return $this;
		}

		/**
		 * @return null
		 */
		public function getRenderer(){
			return $this->getOption('renderer');
		}

	}
}

