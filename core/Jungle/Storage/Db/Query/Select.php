<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 14:48
 */
namespace Jungle\Storage\Db\Query {

	use Jungle\Storage\Db\Query\Entity\AggregateFunction;
	use Jungle\Storage\Db\Query\Entity\Alias;
	use Jungle\Storage\Db\Query\Entity\Column;
	use Jungle\Storage\Db\Query\Entity\Table;
	use Jungle\Storage\Db\Query\Select\Join;

	/**
	 * Class Select
	 * @package Jungle\Storage\Db\Query
	 */
	class Select{

		/** @var  Table */
		protected $table;

		/** @var  Column[]|AggregateFunction[] */
		protected $columns = [];

		/** @var int */
		protected $limit = 100;

		/** @var  int */
		protected $offset = 0;

		/** @var Column[] */
		protected $order_by = [];

		/** @var string[]|int[] */
		protected $order_by_directions = [];

		/** @var Column[]  */
		protected $group_by = [];

		/** @var Join[] */
		protected $joins = [];

		/** @var array  */
		protected $where = null;

		/** @var array  */
		protected $having = null;

		/**
		 * @param Column $column
		 * @param $direction
		 */
		public function addOrderBy(Column $column, $direction){

		}

		/**
		 * @param Column $column
		 */
		public function addGroupBy(Column $column){

		}

		/**
		 * @param $limit
		 * @param null $offset
		 * @return $this
		 */
		public function setLimit($limit, $offset = null){
			if($offset!==null){
				$this->setOffset($offset);
			}
			$this->limit = $limit;
			return $this;
		}

		/**
		 * @param $offset
		 * @return $this
		 */
		public function setOffset($offset){
			$this->offset = $offset;
			return $this;
		}

		/**
		 * @param Entity $entity
		 * @return $this
		 */
		public function addColumn(Entity $entity){
			if($entity instanceof Alias &&
			   (!$entity->getEntity() instanceof Column || !$entity->getEntity() instanceof AggregateFunction)){
				throw new \InvalidArgumentException('Entity is not Column or AggregateFunction');
			}
			if($this->searchColumn($entity)===false){
				$this->columns[] = $entity;
			}
			return $this;
		}

		public function removeColumn(Entity $entity){
			$i = $this->searchColumn($entity);
			if($i!==false){
				array_splice($this->columns,$i,1);
			}
			return $this;

		}

		public function searchColumn(Entity $entity){
			return array_search($entity,$this->columns,true);
		}




	}
}

