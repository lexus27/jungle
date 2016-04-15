<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 15:26
 */
namespace Jungle\Storage\Db\Lexer {

	use Jungle\Smart\Keyword\Manager as Mgr;
	use Jungle\Smart\Keyword\Storage;
	use Jungle\Storage\Db\Lexer\SignManager\SignPool;
	use Jungle\Storage\Db\Lexer\SignManager\SignTypePool;

	/**
	 * Class Pool
	 * @package Jungle\Storage\Db\Lexer
	 */
	class SignManager extends Mgr{

		/**
		 * @param Storage $storage
		 */
		public function __construct(Storage $storage){
			$this->addPool((new SignPool($storage)));
			$this->addPool((new SignTypePool($storage)));
		}

	}
}

