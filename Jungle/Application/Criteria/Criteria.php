<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 16:13
 */
namespace Jungle\Application\Criteria {

	/**
	 * Class Criteria
	 * @package Jungle\Application\Criteria
	 */
	class Criteria{

		public $offset;

		public $limit;

		public $condition;

		public $order;


		public function setLimit($limit){
			$this->limit = $limit;
			return $this;
		}
		public function setOffset($offset){
			$this->offset = $offset;
			return $this;
		}

		public function setCondition(array $condition){
			$this->condition = $condition;
		}
		public function addCondition(array $condition, $operator = 'AND'){
			$this->condition = $this->condition? array_merge($this->condition, [$operator, $condition] ) : $condition;
			return $this;
		}

		public function setOrder(array $sort_fields){
			$this->order = $sort_fields;
			return $this;
		}


		/**
		 * @param $field_name
		 * @param string $direction
		 * @return $this
		 */
		public function prependOrder($field_name, $direction = 'ASC'){
			if(!$this->order){
				$this->order = [];
			}
			array_splice($this->order,0,0,[
				$field_name => $direction
			]);
			return $this;
		}


		/**
		 * @param $field_name
		 * @param string $direction
		 * @return $this
		 */
		public function appendOrder($field_name, $direction = 'ASC'){
			if(!$this->order){
				$this->order = [];
			}
			array_splice($this->order,count($this->order),0,[
				$field_name => $direction
			]);
			return $this;
		}


	}
}

