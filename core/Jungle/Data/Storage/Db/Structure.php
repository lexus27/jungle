<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 01.03.2016
 * Time: 15:50
 */
namespace Jungle\Data\Storage\Db {

	use Jungle\Data\Storage\Db\Structure\Database;

	/**
	 * Class Structure
	 * @package Jungle\Data\Storage\Db
	 */
	class Structure{

		/** @var Adapter */
		protected $adapter;

		/** @var Database[] */
		protected $databases = [];


		/**
		 * @return Structure\Database[]
		 */
		public function getDatabases(){
			return $this->databases;
		}

		/**
		 * @param $name
		 * @return Structure\Database
		 */
		public function newDatabase($name){

		}

		/**
		 * @param $name
		 * @return Structure\Database|null
		 */
		public function getDatabase($name){

		}

		/**
		 * @param Database $database
		 * return $this
		 */
		public function addDatabase(Database $database){

		}

		/**
		 * @param Database $database
		 * return $this
		 */
		public function searchDatabase(Database $database){

		}

		/**
		 * @param Database $database
		 * return $this
		 */
		public function removeDatabase(Database $database){}


		public function save(){
			foreach($this->databases as $database){
				$database->save();
			}
		}

	}
}

