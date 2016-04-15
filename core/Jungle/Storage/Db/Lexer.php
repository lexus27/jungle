<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 13:39
 */
namespace Jungle\Storage\Db {

	use Jungle\Storage\Db\Lexer\Sign;
	use Jungle\Storage\Db\Lexer\SignManager;
	use Jungle\Storage\Db\Lexer\SqlContext;

	/**
	 * Class Lexer
	 * @package Jungle\Storage\Db
	 *
	 * Определяющий модуль для парсинга и контроля SQL запросов
	 *
	 */
	class Lexer{

		/** @var SignManager  */
		protected $signManager;

		/**
		 * @param SignManager $manager
		 * @return $this
		 */
		public function setSignManager(SignManager $manager){
			$this->signManager = $manager;
			return $this;
		}

		public function getSignManager(){
			return $this->signManager;
		}

		/**
		 * @param $tokenName
		 * @return Sign
		 */
		public function getSign($tokenName){
			return $this->signManager->getPool('SignPool')->get($tokenName);
		}


		/**
		 * @param $sql
		 * @return SqlContext
		 */
		public function createContext($sql){
			return new SqlContext($sql,$this);
		}

		/**
		 * @param $sql
		 * @return false|Lexer\Token
		 */
		public function recognize($sql){
			if($sql instanceof Sql){
				$sql = $sql->getSql();
			}
			$context = $this->createContext($sql);
			/** @var Sign $token */
			foreach($this->getSignManager()->getPool('SignPool')->getKeywords() as $token){
				if(($recognized = $token->recognize($context))){
					return $recognized;
				}
			}
			return false;
		}

	}
}

