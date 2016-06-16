<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 15:26
 */
namespace Jungle\Data\Storage\Db\Lexer {

	use Jungle\Data\Storage\Db\Lexer\SignManager\SignPool;
	use Jungle\Data\Storage\Db\Lexer\SignManager\SignTypePool;
	use Jungle\Smart\Keyword\Manager as Mgr;
	use Jungle\Smart\Keyword\Storage;

	/**
	 * Class Pool
	 * @package Jungle\Data\Storage\Db\Lexer
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

