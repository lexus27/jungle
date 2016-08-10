<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 14:55
 */
namespace Jungle\Data\Storage\Db\Lexer {

	use Jungle\Util\INamed;

	/**
	 * Class SignGroup
	 * @package Jungle\Data\Storage\Db\Lexer
	 */
	class SignGroup implements ISign, INamed{

		/** @var array */
		protected $brackets = ['(',')'];

		/** @var string */
		protected $delimiter = ',';

		/** @var bool */
		protected $delimiter_ending_allowed = false;

		/** @var string|null */
		protected $name;

		/** @var  Sign|SignList|array|string */
		protected $sign;


		/**
		 * @param null $name
		 * @param array|string|Sign|SignList $sign
		 */
		public function __construct($name =  null, $sign = null){
			$this->name = $name;
			$this->sign = $sign;
		}

		/**
		 * @return null|string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 * @return null|string
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @param array|string|Sign|SignList $token
		 * @return $this
		 */
		public function setSign($token){
			$this->sign = $token;
			return $this;
		}

		/**
		 * @param array|string|Sign|SignList $list
		 * @return $this
		 */
		public function setSignList(SignList $list){
			$this->sign = $list;
			return $this;
		}

		/**
		 * @param $delimiter
		 * @param null $endingAllowed
		 * @return $this
		 */
		public function setDelimiter($delimiter, $endingAllowed = null){
			$this->delimiter = $delimiter;
			if($endingAllowed!==null){
				$this->setDelimiterEndingAllowed($endingAllowed);
			}
			return $this;
		}

		/**
		 * @param $allowed
		 * @return $this
		 */
		public function setDelimiterEndingAllowed($allowed){
			$this->delimiter_ending_allowed = boolval($allowed);
			return $this;
		}

		/**
		 * @param $bracketLeft
		 * @param $bracketRight
		 * @return $this
		 */
		public function setBrackets($bracketLeft, $bracketRight){
			$this->brackets = [$bracketLeft,$bracketRight];
			return $this;
		}


		/**
		 * @param $position
		 * @param SqlContext $context
		 * @param null $dialect
		 * @param null $nextPoint
		 * @return false|TokenGroup
		 */
		public function recognize(SqlContext $context, $position=0,$dialect = null,& $nextPoint = null){
			$r = $context->matchStartWith($position, preg_quote($this->brackets[0]),true,true);
			if($r === false){
				return false;
			}
			$nextPoint = $r[2];
			$holders = new TokenGroup($this);

			$sign = Sign::getSign($context,$this->sign);

			while(($token = $sign->recognize($context,$nextPoint,$dialect,$nextPoint))){
				$holders->addToken($token);
				$r = $context->matchStartWith($nextPoint,preg_quote(','),true,true);
				if($r===false){
					break;
				}else{
					$nextPoint = $r[2];
				}
			}
			if(count($holders)===0){
				return false;
			}
			$r = $context->matchStartWith($nextPoint, preg_quote($this->brackets[1]),true,true);
			if($r === false){
				return false;
			}
			$nextPoint = $r[2];
			return $holders;
		}

	}
}

