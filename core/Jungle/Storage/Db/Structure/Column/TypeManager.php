<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 11:21
 */
namespace Jungle\Storage\Db\Structure\Column {

	use Jungle\Smart\Keyword\Factory;
	use Jungle\Smart\Keyword\Pool;
	use Jungle\Smart\Keyword\Storage;

	/**
	 * Class TypePool
	 * @package Jungle\Storage\Db\Structure\Column
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
				'directory' => __DIR__ . DIRECTORY_SEPARATOR . 'Types' . DIRECTORY_SEPARATOR,
				'extension' => '.key.php'
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

