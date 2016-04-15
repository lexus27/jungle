<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 14:31
 */
namespace Jungle\Storage\Db\Lexer {

	use Jungle\Basic\INamed;

	/**
	 * Class Token
	 * @package Jungle\Storage\Db\Lexer
	 */
	class Token implements INamed{

		/** @var SqlContext  */
		protected $context;

		/** @var Sign  */
		protected $sign;

		/** @var  string */
		protected $recognized;

		/** @var  int */
		protected $position;

		/** @var  Token[]|TokenGroup[] */
		protected $after_sequence = [];

		/** @var  Token[]|TokenGroup[] */
		protected $before_sequence = [];

		/**
		 * @Constructor
		 *
		 * @param SqlContext $context
		 * @param Sign $token
		 * @param $recognized
		 * @param $position
		 */
		public function __construct(SqlContext $context, Sign $token, $recognized, $position){
			$this->context      = $context;
			$this->sign         = $token;
			$this->recognized   = $recognized;
			$this->position     = $position;
		}

		/**
		 * @param Token[]|TokenGroup[] $sequence
		 * @return $this
		 */
		public function setAfterSequence($sequence){
			$this->after_sequence = $sequence;
			return $this;
		}

		/**
		 * @param Token[]|TokenGroup[] $sequence
		 * @return $this
		 */
		public function setBeforeSequence($sequence){
			$this->before_sequence = $sequence;
			return $this;
		}

		/**
		 * @return Sign
		 */
		public function getSign(){
			return $this->sign;
		}

		/**
		 * @return string
		 */
		public function getRecognized(){
			return $this->recognized;
		}

		/**
		 * @return SqlContext
		 */
		public function getContext(){
			return $this->context;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->sign->getName();
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			return $this;
		}

		/**
		 * @return Token[]|TokenGroup[]
		 */
		public function getAfterSequence(){
			return $this->after_sequence;
		}

		/**
		 * @return Token[]|TokenGroup[]
		 */
		public function getBeforeSequence(){
			return $this->before_sequence;
		}

	}
}

