<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 11:21
 */
namespace Jungle\Data\Storage\Db\Structure\Column {

	use Jungle\Util\Smart\Keyword\Factory;
	use Jungle\Util\Smart\Keyword\Pool;
	use Jungle\Util\Smart\Keyword\Storage;

	/**
	 * Class TypePool
	 * @package Jungle\Data\Storage\Db\Structure\Column
	 */
	class TypePool extends Pool{

		/** @var  bool */
		protected $dummy_allowed    = true;

		/** @var  bool */
		protected $case_insensitive = false;

		/**
		 * @param $alias
		 * @param Storage $storage
		 */
		public function __construct($alias,Storage $storage=null){
			parent::__construct('column_type_manager',$storage===null?new Storage\ArrayFile([
				'directory' => __DIR__ . DIRECTORY_SEPARATOR . 'types' . DIRECTORY_SEPARATOR,
				'extension' => '.php'
			]):$storage);
		}


		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function(){
					return new Type();
				});
			}
			return parent::getFactory();
		}

	}
}

