<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.01.2017
 * Time: 5:16
 */
namespace Jungle\Data\Record\Locator {

	use Jungle\Data\Record\Relation\RelationForeign;
	use Jungle\Data\Record\Relation\RelationMany;
	use Jungle\Data\Record\Relation\RelationSchema;
	use Jungle\Data\Record\Relation\RelationSchemaHost;
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class Point
	 * @package Jungle\Data\Record\Locator
	 */
	class Point implements Locator{

		/** @var  Schema */
		public $schema;

		/** @var  string */
		public $path;

		/** @var  RelationSchema */
		public $relation;

		/** @var  Point */
		public $prev;

		/** @var  string */
		public $reversed_path;

		/** @var bool Находится в зависимой от FK отношении */
		public $in_host = false;

		/** @var bool Находится в множественном отношении */
		public $to_many = false;

		/** @var bool Опенер находится в множественном отношении относительно дестинейшна */
		public $reversed_to_many = false;

		public $circular = false;

		public $recursive = false;

		/**
		 * @return Schema
		 */
		public function getPrevSchema(){
			return $this->relation->schema;
		}

		/**
		 * @param null $default_path
		 * @return null|string
		 */
		public function getPrevPath($default_path = null){
			return $this->prev?$this->prev->path:$default_path;
		}

		/**
		 * @return bool
		 */
		public function isMany(){
			return $this->relation instanceof RelationMany;
		}

		/**
		 * @return bool
		 */
		public function isLocal(){
			return !$this->prev;
		}

		public function hasHost(){
			return $this->in_host;
		}

		public function hasMany(){
			return $this->to_many;
		}

		/**
		 * @return bool|null
		 */
		public function hasManyReversed(){
			return $this->reversed_to_many;
		}

		/**
		 * @return Schema
		 */
		public function getOpeningSchema(){
			return !$this->prev?$this->relation->schema:$this->prev->getOpeningSchema();
		}

		/**
		 * @inheritDoc
		 * @return Point[]|array
		 */
		public function line($reversed = false, $collector = null, $collector_ordered = false){
			$a = [];
			$point = $this;

			if($collector){
				if(!$reversed && $collector_ordered){
					while($point){
						$a[] = $point;
						$point = $point->prev;
					}
					if(is_callable($collector)){
						foreach($a as &$point){
							$a[] = call_user_func($collector, $point);
						}
					}elseif(is_string($collector)){
						foreach($a as &$point){
							$a[] = call_user_func([$point,$collector]);
						}
					}elseif(is_array($collector)){
						$m_name = array_shift($collector);
						foreach($a as &$point){
							$a[] = call_user_func_array([$point,$m_name],$collector);
						}
					}else{
						// error
					}
					return $a;
				}else{
					if($collector && is_callable($collector)){
						while($point){
							$a[] = call_user_func($collector, $point);
							$point = $point->prev;
						}
					}elseif(is_string($collector)){
						while($point){
							$a[] = call_user_func([$point,$collector]);
							$point = $point->prev;
						}
					}elseif(is_array($collector)){
						$m_name = array_shift($collector);
						while($point){
							$a[] = call_user_func_array([$point,$m_name],$collector);
							$point = $point->prev;
						}
					}else{
						while($point){
							$a[] = $point;
							$point = $point->prev;
						}
					}
				}
			}else{
				while($point){
					$a[] = $point;
					$point = $point->prev;
				}
			}
			return $reversed?$a:array_reverse($a);
		}


		/**
		 * @return Point
		 */
		public function getReversed(){
			return $this->schema->getPoint($this->getReversedPath());
		}

		/**
		 * @return null|string
		 */
		public function getReversedPath(){
			if($this->reversed_path === null){
				$this->reversed_path = $this->matchReversedPath();
			}
			return $this->reversed_path;
		}

		/**
		 * @return null|string
		 */
		public function getReversedName(){
			if($this->relation instanceof RelationSchemaHost){
				$referenced_relation = $this->relation->referenced_relation;
				return $referenced_relation->name;
			}elseif($this->relation instanceof RelationForeign){
				$referenced_relations = $this->relation->getOwnerships($this->schema);
				foreach($referenced_relations as $referenced_relation){
					return $referenced_relation->name;
				}
			}
			return null;
		}

		/**
		 * @return null|string
		 */
		public function matchReversedPath(){
			if($this->relation instanceof RelationSchemaHost){
				$referenced_relation = $this->relation->referenced_relation;
				$reversed_name = $referenced_relation->name;
				$prev_reversed = $this->prev? $this->prev->matchReversedPath():null;
				return $reversed_name.($prev_reversed?'.'.$prev_reversed:'');
			}elseif($this->relation instanceof RelationForeign){
				$referenced_relations = $this->relation->getOwnerships($this->schema);
				foreach($referenced_relations as $referenced_relation){
					$reversed_name = $referenced_relation->name;
					$prev_reversed =$this->prev? $this->prev->matchReversedPath():null;
					return $reversed_name.($prev_reversed?'.'.$prev_reversed:'');
				}
			}
			return false;
		}

		/**
		 * @return array {field} => {referenced_field}
		 */
		public function getCollation(){
			return array_combine(
				$this->relation->fields, $this->relation->getReferencedFields()
			);
		}

		public function getSchema(){
			return $this->schema;
		}

		public function isRecursive(){
			return $this->recursive;
		}

		public function isCircular(){
			return $this->circular;
		}

		public function hasReversed(){
			return !!$this->reversed_path;
		}


	}
}

