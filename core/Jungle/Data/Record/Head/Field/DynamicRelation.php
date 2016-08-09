<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.07.2016
 * Time: 6:01
 */
namespace Jungle\Data\Record\Head\Field {

	/**
	 * Class DynamicRelation
	 * @package Jungle\Data\Record\Head\Field
	 */
	class DynamicRelation extends Relation{

		/** @var  string */
		protected $referenced_schemafield;

		/** @var  array */
		protected $allowed_referenced_schemas = null;

		protected $allowed_referenced_schemas_fields = [];


		/**
		 * @param array|null $schemaNames
		 * @return $this
		 */
		public function setAllowedReferencedSchemas(array $schemaNames = null){
			$this->allowed_referenced_schemas = $schemaNames;
			return $this;
		}

		/**
		 * @param $schemaName
		 * @param array $fields
		 * @return $this
		 */
		public function setReferencedSchemasFields($schemaName, array $fields){
			$this->allowed_referenced_schemas_fields[$schemaName] = $fields;
			return $this;
		}

		/**
		 * @param null $schemaName
		 * @return \string[]
		 */
		public function getReferencedFields($schemaName = null){
			if($schemaName === null){
				return $this->referenced_fields;
			}else{
				if(isset($this->allowed_referenced_schemas_fields[$schemaName])){
					return $this->allowed_referenced_schemas_fields[$schemaName];
				}else{
					return $this->referenced_fields;
				}
			}
		}

	}
}

