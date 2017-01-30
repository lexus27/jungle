<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.01.2017
 * Time: 5:49
 */
namespace Jungle\Data\Record\Locator {

	use Jungle\Data\Record\Relation\RelationMany;
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class Property
	 * @package Jungle\Data\Record\Locator
	 */
	abstract class Property implements Locator{

		/** @var  Schema */
		public $opening_schema;

		/** @var  Point|null */
		public $point;

		/** @var  string */
		public $path;

		/** @var  string */
		public $field;

		/**
		 * @return Schema
		 */
		public function getSchema(){
			return $this->point?$this->point->schema:$this->opening_schema;
		}

		/**
		 * @return bool
		 */
		public function hasReversed(){
			return $this->point?$this->point->hasReversed():false;
		}

		/**
		 * @return Schema
		 */
		public function getOpeningSchema(){
			return $this->opening_schema;
		}

		/**
		 * @return bool
		 */
		public function hasMany(){
			return $this->point && $this->point->hasMany();
		}

		/**
		 * @return bool
		 */
		public function hasManyReversed(){
			return $this->point && $this->point->hasManyReversed();
		}

		/**
		 * @return bool
		 */
		public function hasHost(){
			return $this->point && $this->point->hasHost();
		}


		/**
		 * @return bool
		 */
		public function isLocal(){
			return !$this->point || !$this->point->prev;
		}

		/**
		 * @return bool
		 */
		public function isMany(){
			return $this->point->relation instanceof RelationMany;
		}

		/**
		 * @return Point
		 */
		public function getReversed(){
			return $this->point->getReversed();
		}

		/**
		 * @return bool|null|string
		 */
		public function getReversedPath(){
			return $this->point?$this->point->getReversedPath():null;
		}

		/**
		 * @return Schema
		 */
		public function getPrevSchema(){
			return $this->point?$this->point->relation->schema:$this->opening_schema;
		}

		/**
		 * @return bool
		 */
		public function isRecursive(){
			return $this->point && $this->point->isRecursive();
		}

		/**
		 * @return bool
		 */
		public function isCircular(){
			return $this->point && $this->point->isCircular();
		}

		/**
		 * @return Point|null
		 */
		public function getPoint(){
			return $this->point;
		}

		/**
		 * @inheritDoc
		 * @return Point[]|array
		 */
		public function line($reversed = false, $collector = null, $collector_ordered = false){
			if($this->point){
				return $this->point->line($reversed,$collector,$collector_ordered);
			}
			return [];
		}


	}
}

