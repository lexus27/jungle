<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 2:01
 */
namespace Jungle\Data\DataMap {

	use Jungle\Data\DataMap\Sorter\SorterInterface;
	use Jungle\Data\Sorter as SimpleSorter;
	use Jungle\Util\Value\Cmp;

	/**
	 * Class Sorter
	 * @package Jungle\Data\DataMap
	 */
	class Sorter extends SimpleSorter implements SorterInterface{

		/** @var  array */
		protected $sort_fields;

		/** @var  Schema */
		protected $schema;

		/**
		 * @param $array
		 * @return bool
		 */
		public function sort(& $array){
			return usort($array,function($a,$b){
				$result = 0;
				$fields = $this->getSortFields();
				foreach($fields as $field_name => $direction){
					$valueA = $this->schema->valueAccessGet($a,$field_name);
					$valueB = $this->schema->valueAccessGet($b,$field_name);
					$result = call_user_func($this->cmp,$valueA,$valueB);
					if($result !== 0){
						$result = $direction === 'DESC'? Cmp::invert($result):$result;
						break;
					}
				}
				return $result;
			});
		}

		/**
		 * @param $fields
		 * @return $this
		 */
		public function setSortFields($fields){
			$this->sort_fields = $fields;
			return $this;
		}

		/**
		 * @return array|null
		 */
		public function getSortFields(){
			return $this->sort_fields;
		}

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			$this->schema = $schema;
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getSchema(){
			return $this->schema;
		}
	}
}

