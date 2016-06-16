<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 05.03.2016
 * Time: 16:24
 */
namespace Jungle\Data\Storage\Db {

	/**
	 * Class Query
	 * @package Jungle\Data\Storage\Db
	 */
	class Query{

		/** @var   */
		protected $table;

		protected $columns = [];

		protected $columns_alias = [];


		/**
		 *
		 */
		public function select(){

		}

		/**
		 * @param $column
		 * @param $alias
		 * @return $this
		 */
		public function column($column, $alias){
			$this->columns[] = $column;
			$this->columns_alias[] = $alias;
			return $this;
		}

		public function from(){

		}

		public function join($table, $alias){

		}

		/**
		 * @param $container_key
		 * @param $identifier
		 */
		public static function contained($container_key, $identifier){

		}


	}
}

