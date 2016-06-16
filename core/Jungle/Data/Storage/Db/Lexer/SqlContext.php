<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 13:42
 */
namespace Jungle\Data\Storage\Db\Lexer {

	use Jungle\Data\Storage\Db\Lexer;

	/**
	 * Class SignManager
	 * @package Jungle\Data\Storage\Db\Lexer
	 */
	class SqlContext{

		/** @var  string */
		protected $sql;

		/** @var  Lexer */
		protected $lexer;

		/**
		 * @param $sql
		 * @param Lexer $lexer
		 */
		public function __construct($sql, Lexer $lexer){
			$this->sql = $sql;
			$this->setLexer($lexer);
		}

		/**
		 * @param $offset
		 * @param $startWith
		 * @param bool|true $trimBefore
		 * @param bool $trimResult
		 * @param null $context
		 * @return bool|string
		 */
		public function matchStartWith($offset, $startWith, $trimBefore = true, $trimResult = false, $context = null){
			if($context===null){
				$context=$this->sql;
			}
			$chunk = substr($context,$offset);
			$startWith = addcslashes($startWith,'@');
			$regExp = '@^'.($trimBefore?"[\\s\r\n\t]*":'')."($startWith)@i";
			if(preg_match($regExp,$chunk,$m,PREG_OFFSET_CAPTURE)){
				if($m[1][0]){
					$result = $trimResult?trim($m[1][0]):$m[1][0];
					$offset = $offset + intval($m[1][1]);
					$rLen = strlen($result);
					return [$offset,$result,$offset+$rLen];
				}
			}
			return false;
		}

		/**
		 * @param $offset
		 * @param $endWith
		 * @param bool|true $trimAfter
		 * @param bool|false $trimResult
		 * @param null $context
		 * @return array|bool
		 */
		public function matchEndWith($offset, $endWith, $trimAfter = true, $trimResult = false, $context = null){
			if($context===null){
				$context=$this->sql;
			}
			$chunk = substr($context,0,$offset);
			$startWith = addcslashes($endWith,'@');
			$regExp = "@($startWith)".($trimAfter?"[\\s\r\n\t]*":'').'@i';
			if(preg_match($regExp,$chunk,$m,PREG_OFFSET_CAPTURE)){
				if($m[1][0]){
					$result = $trimResult?trim($m[1][0]):$m[1][0];
					$offset = $offset + intval($m[1][1]);
					$rLen = strlen($result);
					return [$offset,$result,$offset+$rLen];
				}
			}
			return false;
		}

		/**
		 * @return Lexer
		 */
		public function getLexer(){
			return $this->lexer;
		}

		/**
		 * @param Lexer $lexer
		 * @return $this
		 */
		public function setLexer(Lexer $lexer){
			$this->lexer = $lexer;
			return $this;
		}

	}
}

