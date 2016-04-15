<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 14:18
 */
namespace Jungle\Storage\Db\Lexer {

	/**
	 * Class SignList
	 * @package Jungle\Storage\Db\Lexer
	 */
	class SignList implements ISign{

		/** @var Sign[] */
		protected $tokens = [];

		/** @var bool  */
		protected $empty_allowed = false;

		/**
		 * @param Sign $token
		 * @return $this
		 */
		public function addToken(Sign $token){
			$this->tokens[] = $token;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getTokens(){
			return $this->tokens;
		}

		/**
		 *
		 */
		public function addEmpty(){
			$this->empty_allowed = true;
		}

		/**
		 * @param $position
		 * @param SqlContext $context
		 * @param null $dialect
		 * @param null $nextPoint
		 * @return false|Token
		 */
		public function recognize(SqlContext $context, $position = 0, $dialect = null, & $nextPoint = null){

			foreach($this->tokens as $token){
				$np = null;
				$result = $token->recognize($context,$position,$dialect,$np);
				if($result){
					$nextPoint = $np;
					return $result;
				}
			}
			if($this->empty_allowed){
				return true;
			}else{
				return false;
			}
		}

	}
}

