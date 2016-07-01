<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.01.2016
 * Time: 0:09
 */
namespace Jungle\Util\Communication\URL\Manager {

	use Jungle\Util\Communication\URL\Scheme;
	use Jungle\Util\Smart\Keyword\Factory;
	use Jungle\Util\Smart\Keyword\Pool;
	use Jungle\Util\Smart\Keyword\Storage;

	/**
	 * Class Pool
	 * @package Jungle\Util\Communication\URL\Scheme
	 */
	class SchemePool extends Pool{

		/** @var bool  */
		protected $case_insensitive = false;

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			parent::__construct('SchemePool',$store);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function(){
					return new Scheme();
				});
			}
			return parent::getFactory();
		}

	}

}

