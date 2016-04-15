<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 05.03.2016
 * Time: 16:24
 */
namespace Jungle\Storage\Db {

	/**
	 * Class Query
	 * @package Jungle\Storage\Db
	 */
	class Query{

		const BUILD_SELECT = 2;

		const BUILD_DELETE = 4;

		const BUILD_UPDATE = 8;

		const BUILD_INSERT = 16;


		const BUILD_JOIN = 32;


		protected $table;

		protected $joins;



		protected $limit = 100;

		protected $offset = 0;

		protected $where = [];

		protected $having = [];

		protected $group_by = [];

		protected $order_by = [];

		protected $_current_join = null;

		protected $_current_building_type = null;

		/**
		 * @param $columns
		 */
		public function select($columns){

		}

		public function insert($into, $columns){

		}



		public function from($table, $alias = null){

		}

		public function leftJoin($table, $alias = null, $on = null){
			$this->_current_join = [
				'table' => $table,
				'as'    => $alias,
				'on'    => $on
			];
		}

		public function rightJoin($table, $alias = null, $on = null){
			$this->_current_join = [
				'table' => $table,
				'as'    => $alias,
				'on'    => $on
			];
		}

		public function innerJoin($table, $alias = null, $on = null){
			$this->_current_join = [
				'table' => $table,
				'as'    => $alias,
				'on'    => $on
			];
		}

		/**
		 * @param $expression
		 * @return $this
		 */
		public function on($expression){
			if($this->_current_building_type | self::BUILD_SELECT && $this->_current_join){
				$this->_current_join['on'] = $expression;
			}
			return $this;
		}

		public function setLimit($limit, $offset = null){

		}

		public function setOffset($offset){

		}

		/**
		 * @param $value1
		 * @param $operator
		 * @param $value2
		 */
		public function having($value1 , $operator, $value2){

		}

	}
}

