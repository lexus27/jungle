<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 15:38
 */
namespace Jungle\Storage\Db\Query\Entity {

	use Jungle\Storage\Db\Query\Entity;

	/**
	 * Class Alias
	 * @package Jungle\Storage\Db\Query\Entity
	 */
	class Alias extends Entity{

		/** @var Entity */
		protected $entity;

		/**
		 * @param Entity $entity
		 * @return $this
		 */
		public function setEntity(Entity $entity){
			$this->entity = $entity;
			return $this;
		}

		/**
		 * @return Entity
		 */
		public function getEntity(){
			return $this->entity;
		}

	}
}

