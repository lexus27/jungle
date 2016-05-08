<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 20:49
 */
namespace Jungle\Storage\Db\Lexer\SignManager {

	use Jungle\Smart\Keyword\Factory;
	use Jungle\Smart\Keyword\Pool;
	use Jungle\Smart\Keyword\Storage;
	use Jungle\Storage\Db\Lexer\SignType;

	/**
	 * Class SignTypePool
	 * @package Jungle\Storage\Db\Lexer
	 */
	class SignTypePool extends Pool{

		/**
		 * @param Storage $storage
		 */
		public function __construct(Storage $storage){
			parent::__construct('SignTypePool',$storage);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function(){
					return new SignType();
				});
			}
			return $this->factory;
		}

	}
}
