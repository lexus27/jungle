<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 13:40
 */
namespace Jungle\Data\Storage\Db\Lexer {

	use Jungle\Util\INamed;
	use Jungle\Util\Smart\Keyword\Keyword;

	/**
	 * Class Entity
	 * @package Jungle\Data\Storage\Db\Lexer
	 */
	class Sign extends Keyword implements ISign, INamed{

		const TYPE_IDENTIFIER   = 1;

		const TYPE_OPERATOR     = 2;

		const TYPE_KEYWORD      = 3;

		const TYPE_VALUE        = 4;

		const TYPE_PLACEHOLDER  = 5;

		/**
		 * @param $type
		 * @return $this
		 */
		public function setType($type){
			if($this->hasRelated('SignTypeManager',$type)){
				$this->setOption('type',$type);
			}else{

			}
			return $this;
		}

		/**
		 * @return SignType
		 */
		public function getType(){
			$type = $this->getRelatedByOption('SignTypeManager','type');
			if(!$type){
				$type = SignType::getDefaultType();
			}
			return $type;
		}

		/**
		 * @param $sequence
		 * @return $this
		 */
		public function setInnerSequence($sequence){
			$this->setOption('inner_sequence', $sequence);
			return $this;
		}

		/**
		 * @return null
		 */
		public function getInnerSequence(){
			return $this->getOption('inner_sequence');
		}

		/**
		 * @param bool|true $beginner
		 * @return $this
		 */
		public function setBeginner($beginner = true){
			$this->setOption('beginner',boolval($beginner));
			return $this;
		}

		/**
		 * @return null
		 */
		public function isBeginner(){
			return $this->getOption('beginner',false);
		}

		/**
		 * @param $recognizer
		 * @return $this
		 */
		public function setRecognizer($recognizer){
			$this->setOption('recognizer',$recognizer);
			return $this;
		}

		/**
		 * @param $filter
		 * @return $this
		 */
		public function setNormalizer($filter){
			$this->setOption('normalizer',$filter);
			return $this;
		}

		/**
		 * @return null
		 */
		public function getNormalizer(){
			return $this->getOption('normalizer');
		}

		/**
		 * @return null
		 */
		public function getRenderer(){
			return $this->getOption('renderer');
		}

		/**
		 * @param $fn
		 * @param $recognized
		 * @return mixed
		 */
		public static function callFn($fn, $recognized){
			return $recognized;
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
		 * @param $dialect
		 * @return null
		 */
		public function getRecognizer($dialect = null){
			if(!$dialect){
				$dialect = 'mysql';
			}
			$recognizer = $this->getOption('recognizer',null);

			if(is_array($recognizer)){
				return isset($recognizer[$dialect])?$recognizer[$dialect]:null;
			}else{
				return $recognizer;
			}
		}

		/**
		 * @param array $sequence
		 * @return $this
		 */
		public function setAfterSequence(array $sequence){
			$this->setOption('after_sequence',$sequence);
			return $this;
		}

		/**
		 * @param array $sequence
		 * @return $this
		 */
		public function setBeforeSequence(array $sequence){
			$this->setOption('before_sequence',$sequence);
			return $this;
		}

		/**
		 * @param $recognized
		 * @return mixed
		 */
		public function normalize($recognized){
			$normalizer = $this->getNormalizer();
			if($normalizer){
				return self::callFn($normalizer,$recognized);
			}else{
				return $this->getType()->normalize($recognized);
			}
		}

		/**
		 * @param $normalized
		 * @return mixed
		 */
		public function render($normalized){
			$fn = $this->getRenderer();
			if($fn){
				return self::callFn($fn,$normalized);
			}else{
				return $this->getType()->render($normalized);
			}
		}


		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->getIdentifier();
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->setIdentifier($name);
			return $this;
		}

		/**
		 * @param SqlContext $context
		 * @param int $position
		 * @param null $dialect
		 * @param null $nextPoint
		 * @param null $contextSource
		 * @return false|Token
		 */
		public function recognize(SqlContext $context, $position = 0, $dialect = null, & $nextPoint = null, $contextSource = null){


			if(!$this->isBeginner()){
				$prevStart = $context->matchEndWith($position,';|^',true,true,$contextSource);
				if($prevStart){
					return false;
				}

			}

			$result = $context->matchStartWith($position,$this->getRecognizer($dialect),true,true,$contextSource);
			if($result!==false){
				list($tokenOffset, $token, $afterOffset) = $result;
				$nextPoint = $afterOffset;

				$innerSequence = $this->getInnerSequence();
				if($innerSequence){

				}


				$token = $this->normalize($token);

				$holder = new Token($context,$this,$token,$tokenOffset);
				$afterCombo = $this->getOption('after_sequence',[]);
				if($afterCombo){
					$combos = [];
					foreach($afterCombo as $element){
						$element = $this->getSign($context,$element);
						$recognizedElement = $element->recognize($context,$nextPoint,$dialect,$nextPoint,$contextSource);
						if($recognizedElement===false){
							return $recognizedElement;
						}elseif($recognizedElement instanceof Token || $recognizedElement instanceof TokenGroup){
							$combos[] = $recognizedElement;
						}
					}
					$holder->setAfterSequence($combos);
				}
				return $holder;
			}
			return false;
		}


		/**
		 * @param SqlContext $context
		 * @param $element
		 * @return array|Sign|SignList|SignGroup
		 */
		public static function getSign(SqlContext $context, $element){
			if(is_string($element)){
				$element = $context->getLexer()->getSign($element);
			}elseif(is_array($element)){
				$tokenList = new SignList();
				if(in_array(null,$element,true)){
					$tokenList->addEmpty();
					$element = array_filter($element,function($v){
						return !!$v;
					});
				}
				foreach($element as $tName){
					if($tName instanceof Sign){
						$tokenList->addToken($tName);
					}else{
						$tokenList->addToken(
							$context->getLexer()->getSign($tName)
						);
					}
				}
				$element = $tokenList;
			}
			return $element;
		}

	}
}

