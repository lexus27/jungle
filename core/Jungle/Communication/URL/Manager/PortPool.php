<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.01.2016
 * Time: 0:09
 */
namespace Jungle\Communication\URL\Manager {

	use Jungle\Communication\URL\Port;
	use Jungle\Smart\Keyword\Factory;
	use Jungle\Smart\Keyword\Pool;
	use Jungle\Smart\Keyword\Storage;

	/**
	 * Class PortPool
	 * @package Jungle\Communication\URL\Port
	 */
	class PortPool extends Pool{

		/** @var bool  */
		protected $dummy_allowed = true;

		/**
		 * @param Storage $store
		 */
		public function __construct(Storage $store){
			parent::__construct('PortPool',$store);
		}

		/**
		 * @return Factory
		 */
		public function getFactory(){
			if(!$this->factory){
				$this->factory = new Factory(function(){
					return new Port();
				});
			}
			return parent::getFactory();
		}


	}

}

