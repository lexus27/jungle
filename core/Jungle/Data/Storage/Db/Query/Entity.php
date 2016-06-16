<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 15:25
 */
namespace Jungle\Data\Storage\Db\Query {

	/**
	 * Class Entity
	 * @package Jungle\Data\Storage\Db\Query
	 */
	abstract class Entity{

		/** @var  string */
		protected $identifier;

		/**
		 * @return string
		 */
		public function getIdentifier(){
			return $this->identifier;
		}

		/**
		 * @param $identifier
		 */
		public function __construct($identifier){
			$this->identifier = $identifier;
		}

	}
}

