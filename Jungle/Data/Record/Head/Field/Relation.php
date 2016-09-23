<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.06.2016
 * Time: 16:36
 */
namespace Jungle\Data\Record\Head\Field {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Head\Field;
	use Jungle\Exception;
	use Jungle\Util\Data\Schema\OuterInteraction\SchemaAwareInterface;

	/**
	 * Class Relation
	 * @package Jungle\Data\Record\Head\Field
	 */
	class Relation extends Field{



		const TYPE_BELONGS      = 'belongs';
		const TYPE_ONE          = 'one'; // depends - unique referenced_fields(referenced schema foreign keys) group
		const TYPE_MANY         = 'many';
		const TYPE_MANY_THROUGH = 'many_through';

		const ACTION_RESTRICT = 'restrict';
		const ACTION_CASCADE = 'cascade';
		const ACTION_SETNULL = 'setnull';

		/** @var  mixed|null  */
		protected $type;

		/** @var  string[]*/
		protected $fields = [];


		/** @var  bool */
		protected $dynamic = false;

		/** @var  array|null  */
		protected $dynamic_allowed_schemas;

		/** @var  array[]  */
		protected $dynamic_allowed_schemas_fields = [];


		/** @var  string|null */
		protected $dynamic_referenced_schemafield; // Для One и Many

		/** @var  array|null */
		protected $dynamic_referenced_opposites; // Для One и Many

		/** @var */
		protected $branch = false; // Для One и Many



		/** @var  string */
		protected $intermediate_schema;

		/** @var  string[]  */
		protected $intermediate_fields = [];

		/** @var  string[]  */
		protected $intermediate_referenced_fields = [];

		/** @var  array|null */
		protected $intermediate_condition;


		protected $dynamic_intermediate_relations;

		protected $dynamic_intermediate_referenced_relations;


		/** @var  string */
		protected $referenced_schema;

		/** @var  string[]  */
		protected $referenced_fields = [];

		/** @var  array|null */
		protected $referenced_condition;


		/** @var int  */
		protected $action_update = self::ACTION_RESTRICT;

		/** @var  bool  */
		protected $virtual_update = false;

		/** @var int  */
		protected $action_delete = self::ACTION_RESTRICT;

		/** @var  bool  */
		protected $virtual_delete = false;

		/**
		 * Relation constructor.
		 * @param $name
		 */
		public function __construct($name){
			$this->name = $name;
			$this->type = null;
		}


		public function isBranch(){
			return $this->branch;
		}


		/**
		 * @return int
		 */
		public function getActionUpdate(){
			return $this->action_update;
		}

		/**
		 * @param int $action_update
		 * @param null $virtual
		 * @return $this
		 */
		public function setActionUpdate($action_update, $virtual = null){
			$this->action_update = $action_update;
			if($virtual!==null){
				$this->virtual_update = $virtual;
			}
			return $this;
		}

		/**
		 * @param $virtual
		 * @return $this
		 */
		public function setVirtualUpdate($virtual){
			$this->virtual_update = $virtual;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isVirtualUpdate(){
			return $this->virtual_update;
		}

		/**
		 * @return int
		 */
		public function getActionDelete(){
			return $this->action_delete;
		}

		/**
		 * @param int $action_delete
		 * @param null $virtual
		 * @return $this
		 */
		public function setActionDelete($action_delete, $virtual = null){
			$this->action_delete = $action_delete;
			if($virtual!==null){
				$this->virtual_delete = $virtual;
			}
			return $this;
		}

		/**
		 * @param $virtual
		 * @return $this
		 */
		public function setVirtualDelete($virtual){
			$this->virtual_delete = $virtual;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isVirtualDelete(){
			return $this->virtual_delete;
		}


		/**
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param array $options
		 * @return $this
		 * @throws RelationError
		 */
		public function belongsTo($referencedSchema, $fields, $referencedFields, array $options = null){
			return $this->define(array_replace([
				'type' => self::TYPE_BELONGS,
			    'fields' => $fields,
			    'referenced_fields' => $referencedFields,
			    'referenced_schema' => $referencedSchema,
			], (array)$options));
		}

		/**
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param array $options
		 * @return $this
		 * @throws RelationError
		 */
		public function hasOne($referencedSchema, $fields, $referencedFields, array $options = null){
			return $this->define(array_replace([
				'type' => self::TYPE_ONE,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			], (array)$options));
		}

		/**
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param array $options
		 * @return $this
		 * @throws RelationError
		 */
		public function hasMany($referencedSchema, $fields, $referencedFields, array $options = null){
			return $this->define(array_replace([
				'type' => self::TYPE_MANY,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			], (array)$options));
		}

		/**
		 * @param $intermediateSchema
		 * @param $referencedSchema
		 * @param $fields
		 * @param $intermediateFields
		 * @param $intermediateReferencedFields
		 * @param $referencedFields
		 * @param array $options
		 * @return Relation
		 * @throws RelationError
		 */
		public function hasManyThrough(
			$intermediateSchema,$referencedSchema, $fields,
			$intermediateFields, $intermediateReferencedFields, $referencedFields,
			array $options = null
		){
			return $this->define(array_replace([
				'type' => self::TYPE_MANY_THROUGH,
				'fields' => $fields,
				'intermediate_fields' => $intermediateFields,
				'intermediate_referenced_fields' => $intermediateReferencedFields,
				'intermediate_schema' => $intermediateSchema,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			], (array)$options));
		}


		/**
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param $referencedSchemafield
		 * @param $referencedOppositeRelations
		 * @param array $options
		 * @return $this
		 * @throws RelationError
		 */
		public function hasOneDynamic(
			$referencedSchema,
			$fields,
			$referencedFields,
			$referencedSchemafield, $referencedOppositeRelations = [],
			array $options = null
		){
			return $this->define(array_replace_recursive([
				'type' => self::TYPE_ONE,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			    'dynamic' => true,
			    'dynamic_config' => [
				    'referenced_schemafield' => $referencedSchemafield,
				    'referenced_opposites' => $referencedOppositeRelations
			    ]
			], (array)$options));
		}

		public function hasManyDynamic(
			$referencedSchema,
			$fields,
			$referencedFields,
			$referencedSchemafield,
			$referencedOppositeRelations = [],
			array $options = null
		){
			return $this->define(array_replace_recursive([
				'type' => self::TYPE_MANY,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
				'dynamic' => true,
				'dynamic_config' => [
					'referenced_schemafield' => $referencedSchemafield,
				    'referenced_opposites' => $referencedOppositeRelations
				]
			], (array)$options));
		}

		/**
		 * @param $schemafield
		 * @param $fields
		 * @param null $defaultReferencedFields
		 * @param array $allowedSchemaNames
		 * @param array $schemasReferencedFields
		 * @param array $options
		 * @return $this
		 * @throws RelationError
		 */
		public function belongsToDynamic(
			$schemafield, $fields, $defaultReferencedFields = null,
			$allowedSchemaNames = [],
			$schemasReferencedFields = [],
			array $options = null
		){
			return $this->define(array_replace_recursive([
				'type' => self::TYPE_BELONGS,
				'fields' => $fields,
				'referenced_fields' => $defaultReferencedFields,
				'dynamic' => true,
				'dynamic_config' => [
					'schemafield' => $schemafield,
					'schemas' => $allowedSchemaNames,
					'schemas_fields' => $schemasReferencedFields,
				]
			], (array)$options));
		}


		/**
		 * @param array $definition
		 * @return $this
		 * @throws RelationError
		 */
		public function define(array $definition){

			parent::define($definition);

			$c = array_replace_recursive([
				'type'                  => null,

				'fields'                => [],

				'referenced_schema'     => null,
				'referenced_fields'     => [],
				'referenced_condition'  => null,

				'intermediate_schema'   => null,
				'intermediate_fields'   => [],
				'intermediate_referenced_fields' => [],
				'intermediate_condition'=> null,


				'branch'                => false,

				'dynamic'               => false,
				'dynamic_config'        => [
					'schemafield'           => null,
					'referenced_schemafield'=> null,
					'referenced_opposites'  => [],
					'schemas'               => null,
					'schemas_fields'        => []
				],

				'default_virtual'       => false,
				'update_rule'           => [
					'virtual'               => false,
					'action'                => self::ACTION_RESTRICT,
				],
				'delete_rule'           => [
					'virtual'               => false,
					'action'                => self::ACTION_RESTRICT,
				]

			],$definition);unset($definition);
			$type = $c['type'];
			if(!in_array($type,[ self::TYPE_BELONGS, self::TYPE_ONE, self::TYPE_MANY, self::TYPE_MANY_THROUGH],true)){
				throw new RelationError('Relation: not allowed relation type('.var_export($type,true).') passed');
			}

			$fields = is_string($c['fields'])?[$c['fields']]:$c['fields'];

			$intermediateFields = is_string($c['intermediate_fields'])?[$c['intermediate_fields']]:$c['intermediate_fields'];
			$intermediateReferencedFields = is_string($c['intermediate_referenced_fields'])?[$c['intermediate_referenced_fields']]:$c['intermediate_referenced_fields'];
			$intermediateSchema = $c['intermediate_schema'];
			$intermediateCondition = $c['intermediate_condition'];

			$referencedSchema = $c['referenced_schema'];
			$referencedFields = is_string($c['referenced_fields'])?[$c['referenced_fields']]:$c['referenced_fields'];
			$referencedCondition = $c['referenced_condition'];


			$dynamic = $c['dynamic'];
			$dynamicConfig = $c['dynamic_config'];

			$branch = $c['branch'];

			$updateRule = $c['update_rule'];
			$deleteRule = $c['delete_rule'];

			if($branch && !in_array($type,[self::TYPE_MANY, self::TYPE_ONE], true)){
				throw new RelationError('Branch not supported in type: '.$type);
			}

			if($type === self::TYPE_MANY_THROUGH){
				if(!is_array($fields) || !is_array($intermediateFields)){
					throw new RelationError('Relation[THROUGH][SELF to INTERMEDIATE]: fields wrong or not supplied - ['.var_export($fields,true).' TO '.var_export($intermediateFields,true).']');
				}
				if(!is_array($intermediateReferencedFields) || !is_array($referencedFields)){
					throw new RelationError('Relation[THROUGH][INTERMEDIATE to REFERENCED]: fields wrong or not supplied - ['.var_export($intermediateReferencedFields,true).' TO '.var_export($referencedFields,true).']');
				}

				if(count($fields) !== count($intermediateFields)){
					throw new RelationError('Relation[THROUGH][SELF to INTERMEDIATE]: fields wrong counts - (['.implode(', ', $fields).'] TO ['.implode(', ', $intermediateFields).'])');
				}
				if(count($intermediateReferencedFields) !== count($referencedFields)){
					throw new RelationError('Relation[THROUGH][INTERMEDIATE to REFERENCED]: fields wrong counts - (['.implode(', ', $intermediateReferencedFields).'] TO ['.implode(', ', $referencedFields).'])');
				}
			}else{
				if($dynamic){

					$this->dynamic = $dynamic;

					if($type === self::TYPE_BELONGS){
						if(is_array($dynamicConfig['schemas']) && empty($dynamicConfig['schemas'])){
							throw new RelationError('Pass dynamic schemas empty array');
						}
						if(!$dynamicConfig['schemafield']){
							throw new RelationError('Dynamic schemafield not passed');
						}
						if(!in_array($dynamicConfig['schemafield'],$this->schema->getFieldNames(), true)){
							throw new RelationError('Dynamic schemafield value is not in schema->fieldNames');
						}
						if(is_array($dynamicConfig['schemas_fields'])){
							foreach($dynamicConfig['schemas_fields'] as $_k => $_){
								if(count($_) !== count($fields)){
									throw new RelationError('Relation[DIRECT]: fields wrong counts - (['.implode(', ', $fields).'] TO ['.implode(', ', $_).'] for "'.$_k.'")');
								}
							}
						}
						$this->dynamic_allowed_schemas = $dynamicConfig['schemas'];
						$this->dynamic_allowed_schemas_fields = $dynamicConfig['schemas_fields'];
						$referencedSchema = $dynamicConfig['schemafield'];
					}else{
						if(!$dynamicConfig['referenced_schemafield']){
							throw new RelationError('Dynamic referenced_schemafield not passed');
						}
						if(!is_array($fields) || !is_array($referencedFields)){
							throw new RelationError('Relation[DIRECT]: fields wrong or not supplied - ['.var_export($fields,true).' TO '.var_export($referencedFields,true).']');
						}
						if(count($fields) !== count($referencedFields)){
							throw new RelationError('Relation[DIRECT]: fields wrong counts - (['.implode(', ', $fields).'] TO ['.implode(', ', $referencedFields).'])');
						}
						$this->dynamic_referenced_opposites = $dynamicConfig['referenced_opposites'];
						$this->dynamic_referenced_schemafield = $dynamicConfig['referenced_schemafield'];
					}
				}else{
					if(!is_array($fields) || !is_array($referencedFields)){
						throw new RelationError('Relation[DIRECT]: fields wrong or not supplied - ['.var_export($fields,true).' TO '.var_export($referencedFields,true).']');
					}
					if(count($fields) !== count($referencedFields)){
						throw new RelationError('Relation[DIRECT]: fields wrong counts - (['.implode(', ', $fields).'] TO ['.implode(', ', $referencedFields).'])');
					}
				}
			}

			$this->type                             = $type;
			$this->fields                           = $fields;
			$this->referenced_schema                = $referencedSchema;
			$this->referenced_fields                = $referencedFields;
			$this->referenced_condition             = $referencedCondition;

			$this->branch = $branch;

			$this->intermediate_schema              = $intermediateSchema;
			$this->intermediate_fields              = $intermediateFields;
			$this->intermediate_referenced_fields   = $intermediateReferencedFields;
			$this->intermediate_condition           = $intermediateCondition;

			if(is_string($deleteRule)){
				$this->action_delete = $c['delete_rule'];
				$this->virtual_delete = $branch || $dynamic?true:$c['default_virtual'];
			}else{
				$this->action_delete = $deleteRule['action'];
				$this->virtual_delete = $branch || $dynamic?true:$deleteRule['virtual'];
			}
			if(is_string($updateRule)){
				$this->action_update = $updateRule;
				$this->virtual_delete = $branch || $dynamic?true:$c['default_virtual'];
			}else{
				$this->action_update = $updateRule['action'];
				$this->virtual_delete = $branch || $dynamic?true:$updateRule['virtual'];
			}

			return $this;
		}

		/**
		 * @param null $type
		 * @return bool
		 */
		public function isThrough($type = null){
			if($type === null){
				$type = $this->type;
			}
			return $type === self::TYPE_MANY_THROUGH;
		}

		/**
		 * @param null $type
		 * @return bool
		 */
		public function isMany($type = null){
			if($type === null){
				$type = $this->type;
			}
			return in_array($type,[self::TYPE_MANY,self::TYPE_MANY_THROUGH],true);
		}
		
		/**
		 * @return bool
		 */
		public function isBelongs(){
			return $this->type === self::TYPE_BELONGS;
		}

		/**
		 * @param Relation $suspect
		 * @param bool|false $withoutThrough
		 * @return bool
		 */
		public function isOppositeRelation(Relation $suspect, $withoutThrough = false){
			$schema_name = $this->schema->getName();

			if(!$this->isAllowedSchema($suspect->schema)){
				return false;
			}

			if($this->dynamic){
				if($this->type !== self::TYPE_BELONGS){
					if($this->referenced_fields !== $suspect->fields){
						return false;
					}
					if(is_array($this->dynamic_referenced_opposites) && !in_array($suspect->name, $this->dynamic_referenced_opposites, true)){
						return false;
					}
				}else{
					$referencedFields = $this->getDynamicReferencedFields($suspect->schema);
					if($referencedFields !== $suspect->fields){
						return false;
					}
					if(is_array($suspect->dynamic_referenced_opposites) && !in_array($this->name, $suspect->dynamic_referenced_opposites, true)){
						return false;
					}
				}
				return true;
			}

			if(
				($suspect->type === self::TYPE_BELONGS && $this->type !== self::TYPE_BELONGS) ||
				($suspect->type !== self::TYPE_BELONGS && $this->type === self::TYPE_BELONGS)
			){
				if(
					$schema_name !== $suspect->referenced_schema ||
				   $this->fields !== $suspect->referenced_fields
				){
					return false;
				}
				return true;
			}elseif(!$withoutThrough && $suspect->isThrough() && $this->isThrough()){
				if(
					$schema_name !== $suspect->referenced_schema ||
					$this->intermediate_schema !== $suspect->intermediate_schema ||
					$this->fields !== $suspect->referenced_fields ||
					$this->intermediate_fields !== $suspect->intermediate_referenced_fields
				){
					return false;
				}
				return true;
			}else{
				return false;
			}
		}


		/**
		 * @param Relation $suspect
		 * @return bool
		 */
		public function isIntermediateRelation(Relation $suspect){
			if($suspect->type === self::TYPE_BELONGS && $this->type === self::TYPE_MANY_THROUGH){
				if(
					$suspect->fields !== $this->intermediate_fields ||
				    $suspect->referenced_fields !== $this->fields ||
				   !$suspect->schema->isDerivativeFrom($this->intermediate_schema)
				){
					return false;
				}
				return true;
			}
			return false;
		}

		/**
		 * @return array
		 */
		public function getAllowedTypes(){
			return [
				self::TYPE_BELONGS,
				self::TYPE_ONE,
				self::TYPE_MANY,
				self::TYPE_MANY_THROUGH
			];
		}

		/**
		 * @param $type
		 * @return bool
		 */
		public function isAllowedType($type){
			return in_array($type, [ self::TYPE_BELONGS, self::TYPE_ONE, self::TYPE_MANY, self::TYPE_MANY_THROUGH ], true);
		}



		/**
		 * @return array
		 */
		public function getFields(){
			return $this->fields;
		}

		/**
		 * @param $condition
		 * @return $this
		 */
		public function setReferencedCondition($condition){
			$this->referenced_condition=$condition;
			return $this;
		}

		/**
		 * @return array|null
		 */
		public function getReferencedCondition(){
			return $this->referenced_condition;
		}

		/**
		 * @return string
		 */
		public function getReferencedSchema(){
			return $this->referenced_schema;
		}



		/**
		 * @return \string[]
		 * @throws Exception
		 */
		public function getReferencedFields(){
			return $this->referenced_fields;
		}



		/**
		 * @return bool
		 */
		public function isDynamic(){
			return $this->dynamic;
		}

		/**
		 * @return null|string
		 */
		public function getDynamicReferencedSchemafield(){
			return $this->dynamic_referenced_schemafield;
		}


		/**
		 * @return null|string
		 */
		public function getDynamicSchemafield(){
			return $this->referenced_schema;
		}


		/**
		 * @param Record\Head\Schema $schema
		 * @return bool
		 */
		public function isAllowedSchema(Record\Head\Schema $schema){
			$names = $schema->getDerivedNames();
			if($this->dynamic && $this->type === self::TYPE_BELONGS){
				foreach($names as $name)
					if(in_array($name, $this->dynamic_allowed_schemas, true))
						return true;
			}else{
				foreach($names as $name)
					if($this->referenced_schema === $name)
						return true;
			}
			return false;
		}

		/**
		 * @param Record\Head\Schema $schema
		 * @return array|bool|\string[]
		 * @throws Exception
		 */
		public function getDynamicReferencedFields(Record\Head\Schema $schema){
			if($schema === null || $this->type !== self::TYPE_BELONGS){
				return $this->referenced_fields;
			}
			if($this->dynamic_allowed_schemas===null){
				return $schema->getName();
			}

			$schemaName = false;
			foreach($schema->getDerivedNames() as $name){
				if(in_array($name, $this->dynamic_allowed_schemas, true)){
					$schemaName = $name;
					break;
				}
			}
			if($schemaName!==false){
				return isset($this->dynamic_allowed_schemas_fields[$schemaName])?$this->dynamic_allowed_schemas_fields[$schemaName]:$this->referenced_fields;
			}else{
				return false;
			}
		}

		/**
		 * @return string
		 */
		public function getIntermediateSchema(){
			return $this->intermediate_schema;
		}

		/**
		 * @param null $condition
		 * @return $this
		 */
		public function setIntermediateCondition($condition=null){
			$this->intermediate_condition = $condition;
			return $this;
		}

		/**
		 * @return array|null
		 */
		public function getIntermediateCondition(){
			return $this->intermediate_condition;
		}

		/**
		 * @return array
		 */
		public function getIntermediateFields(){
			return $this->intermediate_fields;
		}

		/**
		 * @return array
		 */
		public function getIntermediateReferencedFields(){
			return $this->intermediate_referenced_fields;
		}

		/**
		 * @param Record $record
		 * @return Record\Collection\Relationship
		 */
		protected function createRelationship($record){
			$schemaManager = $this->schema->getSchemaManager();
			$relationship = new Record\Collection\Relationship();
			$relationship->setAncestor($schemaManager->getCollection($this->referenced_schema));
			$relationship->setHolder($record, $this);
			return $relationship;
		}

		/**
		 * @param \Jungle\Data\Record $data
		 * @param $key
		 * @return mixed|null
		 * @throws Record\Exception\Field
		 */
		public function valueAccessGet($data, $key){
			if(!$this->type){
				throw new Record\Exception\Field("Relation Field {$this->name} is not configured, please setup one of relation type[belongsTo,hasOne,hasMany,hasOneTrough,hasManyTrough], At the stage of factory!");
			}
			if(!$data instanceof Record){
				throw new Record\Exception\Field('Relation field valueAccessGet($data,$key) - $data must be Record instance');
			}
			if($data->getOperationMade() === Record::OP_CREATE){
				if($this->isMany()){
					return $this->createRelationship($data);
				}else{
					return null;
				}
			}

			if(!$this->dynamic){
				if($this->type === self::TYPE_MANY_THROUGH || $this->type === self::TYPE_MANY){
					return $this->createRelationship($data);
				}else{
					$schemaManager = $this->schema->getSchemaManager();
					$referencedSchema = $schemaManager->getSchema($this->referenced_schema);

					$condition = [];
					foreach($this->referenced_fields as $i => $name){
						$value = $data->getProperty($this->fields[$i]);
						$condition[] = [$name,'=',$value];
					}
					$condition = array_merge($condition,(array)$this->referenced_condition);

					return $referencedSchema->loadFirst($condition);
				}
			}else{
				if($this->type === self::TYPE_BELONGS){
					$schema = $data->getSchema();
					$schemaManager = $schema->getSchemaManager();

					$referencedSchemaName = $data->getProperty($this->referenced_schema);
					$schemasFields = [];
					if(isset($schemasFields[$referencedSchemaName])){
						$referencedSchemaFields = $schemasFields[$referencedSchemaName];
					}else{
						$referencedSchemaFields = $this->referenced_fields;
					}

					$referencedSchema = $schemaManager->getSchema($referencedSchemaName);

					$condition = [];
					foreach($referencedSchemaFields as $i => $name){
						$value = $data->getProperty($this->fields[$i]);
						$condition[] = [$name,'=',$value];
					}
					$condition = array_merge($condition,(array)$this->referenced_condition);

					return $referencedSchema->loadFirst($condition);
				}elseif($this->type === self::TYPE_ONE){
					$schema = $data->getSchema();
					$schemaManager = $schema->getSchemaManager();

					$schemaName = $schema->getName();
					$referencedSchema = $schemaManager->getSchema($this->referenced_schema);

					$condition = [];
					foreach($this->referenced_fields as $i => $name){
						$value = $data->getProperty($this->fields[$i]);
						$condition[] = [$name,'=',$value];
					}
					$condition[] = [ $this->dynamic_referenced_schemafield , '=' , $schemaName];
					$condition = array_merge($condition,(array)$this->referenced_condition);
					return $referencedSchema->loadFirst($condition);
				}else{
					return $this->createRelationship($data);
				}
			}


		}

		/**
		 * @param Record $data
		 * @param $key
		 * @param $value
		 * @return mixed
		 * @throws Record\Exception\Field
		 */
		public function valueAccessSet($data, $key, $value){
			if(!$this->type){
				throw new Record\Exception\Field("Relation Field {$this->name} is not configured, please setup one of relation type[belongsTo,hasOne,hasMany,hasOneTrough,hasManyTrough], At the stage of factory!");
			}
			return $data instanceof Record? $data->getOriginalData() : $data;
		}

		/**
		 * @param $original_value
		 * @return mixed|void
		 * @throws Record\Exception\Field
		 */
		public function evaluate($original_value){
			throw new Record\Exception\Field('Evaluate not support in relation field');
		}

		/**
		 * @param $native_value
		 * @return mixed|void
		 * @throws Record\Exception\Field
		 */
		public function originate($native_value){
			throw new Record\Exception\Field('Originate not support in relation field');
		}

		/**
		 * @param Record|Relationship|SchemaAwareInterface|null $native_value
		 * @return bool
		 * @throws Record\Exception\Field
		 */
		public function validate($native_value){
			if(!$this->type){
				throw new Record\Exception\Field("Relation Field {$this->name} is not configured, please setup one of relation type[belongsTo,hasOne,hasMany,hasOneTrough,hasManyTrough], At the stage of factory!");
			}
			if($native_value === null && $this->isDefaultNull()){
				return true;
			}
			if($native_value instanceof Record){
				if($this->isMany()){
					return false; // Значение является одиночным, что противоречит типу связи [MULTIPLY].
				}
				$schema = $native_value->getSchema();
			}elseif($native_value instanceof Relationship){
				if(!$this->isMany()){
					return false; // Значение является множественным, что противоречит типу связи {SINGLE}.
				}
				$schema = $native_value->getSchema();
			}else{
				return false; // Значение не может быть NULL
			}
			if(!$this->isAllowedSchema($schema)){
				return false; // Значение не подходит по схеме.
			}
			return true;
		}

		/**
		 * @param $type
		 * @return $this
		 */
		public function setType($type){
			$this->type = $type;
			return $this;
		}

		public function stabilize($value){
			return $value;
		}


		/** @var null|Relation[]  */
		protected $opposite_relations = null;

		/** @var null|Relation[]  */
		protected $intermediate_relations = null;

		/**
		 * @param Record $record
		 * @return Relation[]|null
		 * @throws Record\Exception\Field
		 * @throws Record\Exception\Field\AccessViolation
		 */
		public function getOppositeRelations(Record $record){
			if($this->dynamic && $this->type === self::TYPE_BELONGS){
				$schemaName = $record->getProperty($this->referenced_schema);
				if($this->opposite_relations === null)$this->opposite_relations = [];
				if(!isset($this->opposite_relations[$schemaName])){
					$this->opposite_relations[$schemaName] = $this->schema->getSchemaManager()
						->getSchema($schemaName)
						->getOppositeRelationsFor($this);
				}
				return $this->opposite_relations[$schemaName];
			}else{
				if($this->opposite_relations === null){
					$this->opposite_relations = $this->schema->getSchemaManager()
						->getSchema($this->referenced_schema)
						->getOppositeRelationsFor($this);
				}
				return $this->opposite_relations;
			}
		}

		/**
		 * @param Record $record
		 * @return Relation[]|null
		 */
		public function getIntermediateRelations(Record $record){
			if($this->intermediate_relations === null){
				$this->intermediate_relations = $this->schema->getSchemaManager()->getSchema($this->referenced_schema)->getIntermediateRelationsFor($this);
			}
			return $this->intermediate_relations;
		}


		/**
		 * @param Record $record
		 * @param array $processed
		 * @param array $changed
		 * @return bool|void
		 */
		public function beforeRecordSave(Record $record, array $processed, array $changed = null){
			parent::beforeRecordSave($record,$processed,$changed);
			if(($changed && in_array($this->name,$changed,true)) || (!$changed && $record->isInitializedProperty($this->name))){
				$related = $record->getProperty($this->name);
				switch($this->type){
					case self::TYPE_BELONGS:
						$this->_saveBelongsBefore($record, $related, isset($processed[$this->name])?$processed[$this->name]:null ,$processed,$changed);
						break;
					case self::TYPE_ONE:
						$this->_saveOneBefore($record, $related, isset($processed[$this->name])?$processed[$this->name]:null,$processed,$changed);
						break;
					case self::TYPE_MANY:
						$related->applyPendingOperations();
						$this->_saveManyBefore($record, $related,$processed,$changed);
						break;
					case self::TYPE_MANY_THROUGH:
						$related->applyPendingOperations();
						$this->_saveManyThroughBefore($record, $related,$processed,$changed);
						break;
				}
			}
			return true;
		}


		/**
		 * @param \Jungle\Data\Record $record
		 * @param array $processed
		 * @param array $changed
		 */
		public function afterRecordSave(Record $record, array $processed, array $changed = null){
			if(($changed && in_array($this->name,$changed,true)) || (!$changed && $record->isInitializedProperty($this->name))){
				$related = $record->getProperty($this->name);
				switch($this->type){
					case self::TYPE_BELONGS:
						$this->_saveBelongsAfter($record, $related, isset($processed[$this->name])?$processed[$this->name]:null ,$processed,$changed);
						break;
					case self::TYPE_ONE:
						$this->_saveOneAfter($record, $related, isset($processed[$this->name])?$processed[$this->name]:null,$processed,$changed);
						break;
					case self::TYPE_MANY:
						$this->_saveManyAfter($record, $related,$processed,$changed);
						break;
					case self::TYPE_MANY_THROUGH:
						$this->_saveManyThroughAfter($record, $related,$processed,$changed);
						break;
				}
			}
			parent::afterRecordSave($record,$processed,$changed);
		}


		/**
		 * @param \Jungle\Data\Record $record
		 */
		public function beforeRecordDelete(Record $record){

			switch($this->type){
				case self::TYPE_BELONGS:

					// если поле имеет опцию OWNED delete, значит что в случае удаления этого объекта удалиться и тот который связан
					// Дополнительно: Рекомендуется на $this->fields UNIQUE индекса в БД, т.к иначе выставленые в других объектах связи на идентичный объект, удалив его, у всех зависимых от него потребуется выставить поля в NULL, такая схема наврядли будет где-либо применима
					// Дополнительно: Отлично подходит к противоположной - ONE связи, по выше указаному дополнению

					break;
				case self::TYPE_ONE:
					if($this->branch){
						$this->_reactSingleForDelete($this->action_delete,$this->virtual_delete,$record);
						return;
					}
					foreach($this->getOppositeRelations($record) as $field){
						$this->_reactSingleForDelete($field->action_delete,$field->virtual_delete,$record);
					}
					break;
				case self::TYPE_MANY:
					if($this->branch){
						$this->_reactCollectionForDelete($this->action_delete,$this->virtual_delete,$record,$record->getProperty($this->name));
						return;
					}
					foreach($this->getOppositeRelations($record) as $field){
						$this->_reactCollectionForDelete($field->action_delete,$field->virtual_delete,$record,$record->getProperty($this->name));
					}
					break;
				case self::TYPE_MANY_THROUGH:
					foreach($this->getIntermediateRelations($record) as $field){
						//$field->_beforeRecordOppositeRemove($record, $this, true);
					}
					// Здесь нужно обработать intermediate записи Удалив их, если FK этого не придусматривают
					// В памяти , требуется просто удалить все загруженые записи intermediate схемы которые находятся в intermediate_registry текущего relationship
					// по запросу в БД , мы можем использовать delete condition = [$this->intermediate_fields = $record->getProperty()]
					// Это же условие используется для содержания intermediate относящихся записей в relationship

					break;
			}

		}

		/**
		 * @param \Jungle\Data\Record $record
		 */
		public function afterRecordDelete(Record $record){

		}




		/**
		 *
		 */
		protected function _reactThroughForDelete(){

		}

		/**
		 * @param $action
		 * @param $virtual
		 * @param Record $holder
		 * @param Relationship $relationship
		 * @throws Record\Exception\Operation
		 */
		protected function _reactCollectionForDelete($action,$virtual,Record $holder, Relationship $relationship){
			switch($action){
				case self::ACTION_RESTRICT:
					if($virtual){
						if($relationship->count()){
							throw new Record\Exception\Operation($holder,Record::OP_DELETE,'Record could not delete, because already use in related records');
						}
					}
					break;
				case self::ACTION_SETNULL:
					if($virtual){
						$relationship->setSyncLevel(Relationship::SYNC_STORE);
					}else{
						$relationship->setSyncLevel(Relationship::SYNC_FULL);
					}
					$relationship->update(
						array_fill_keys(
							$relationship
								->getHolderField()
								->getReferencedFields()
							,null
						)
					);
					$relationship->setSyncLevel();
					break;
				case self::ACTION_CASCADE:
					if($virtual){
						$relationship->setSyncLevel(Relationship::SYNC_STORE);
					}else{
						$relationship->setSyncLevel(Relationship::SYNC_FULL);
					}
					$relationship->remove();
					$relationship->setSyncLevel();
					break;

			}
		}

		/**
		 * @param $action
		 * @param $virtual
		 * @param Record $holder
		 * @throws Record\Exception
		 */
		protected function _reactSingleForDelete($action,$virtual, Record $holder){
			switch($action){
				case self::ACTION_RESTRICT:
					if($virtual){
						$related = $holder->getProperty($this->name);
						if($related){
							throw new \LogicException('Record could not delete, because already use in related records');
						}
					}
					break;
				case self::ACTION_SETNULL:
					$related = $holder->getProperty($this->name);
					if($related){
						$collection = $related->getSchema()->getCollection();
						if($virtual){
							$collection->setSyncLevel(Relationship::SYNC_STORE);
						}else{
							$collection->setSyncLevel(Relationship::SYNC_FULL);
						}
						$collection->update(
							array_fill_keys($this->referenced_fields,null),
							[$related->getSchema()->getPrimaryFieldName() => $related->getIdentifierValue()],
							true
						);
						$collection->setSyncLevel();
					}
					break;
				case self::ACTION_CASCADE:
					$related = $holder->getProperty($this->name);
					if($related){
						$collection = $related->getSchema()->getCollection();
						if($virtual){
							$collection->setSyncLevel(Relationship::SYNC_STORE);
						}else{
							$collection->setSyncLevel(Relationship::SYNC_FULL);
						}
						$collection->remove([$related->getSchema()->getPrimaryFieldName() => $related->getIdentifierValue()]);
						$collection->setSyncLevel();
					}
					break;

			}
		}



		/**
		 * @param \Jungle\Data\Record $record
		 * @param \Jungle\Data\Record|null $related
		 * @param \Jungle\Data\Record|null $old
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Field
		 * @throws Record\Exception\Field\AccessViolation
		 * @throws Record\Exception\Field\ReadonlyViolation
		 * @throws Record\Exception\Field\UnexpectedValue
		 * @throws Record\Exception\RelatedRecordError
		 */
		protected function _saveBelongsBefore(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){
			if($old){
				// Что же делать со старой записью, обычно её вообще не трогают,
				// Но у неё могут быть выставленны связаные поля на текущий $record
			}

			if($related){
				if(!$related->save()){
					throw new Record\Exception\RelatedRecordError('Related[BELONGS] record "' . $this->name . '" is not can save');
				}else{
					foreach($this->fields as $i => $name){
						$record->setProperty($name, $related->getProperty($this->referenced_fields[$i]));
					}
					if($this->dynamic){
						$record->setProperty($this->referenced_schema, $related->getSchema()->getName());
					}
				}
			}else{
				if($old){
					if(!$old->save()){
						throw new Record\Exception\RelatedRecordError('Detach error Related[BELONGS] record "' . $this->name . '"');
					}
				}
				foreach($this->fields as $i => $name){
					$record->setProperty($name, null);
				}
				$old->save();
			}
		}

		/**
		 * @param Record $record
		 * @param Record|null $related
		 * @param Record|null $old
		 * @param array $processed
		 * @param array $changed
		 */
		protected function _saveBelongsAfter(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){

		}

		/**
		 * @param Record $record
		 * @param \Jungle\Data\Record|null $related
		 * @param Record|null $old
		 * @param array $processed
		 * @param array $changed
		 */
		protected function _saveOneBefore(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){

		}

		/**
		 * @param Record $record
		 * @param Record|null $related
		 * @param Record|null $old
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Field
		 * @throws Record\Exception\Field\AccessViolation
		 * @throws Record\Exception\Field\ReadonlyViolation
		 * @throws Record\Exception\Field\UnexpectedValue
		 * @throws Record\Exception\RelatedRecordError
		 */
		protected function _saveOneAfter(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){
			if($old){
				if(!$old->save()){
					throw new Record\Exception\RelatedRecordError('Excluding Related[ONE] record "' . $this->name . '" is not can save');
				}
			}
			if($related){
				foreach($this->referenced_fields as $i => $name){
					$related->setProperty($name, $record->getProperty($this->fields[$i]));
				}
				if(!$related->save()){
					throw new Record\Exception\RelatedRecordError('Related[ONE] record "' . $this->name . '" is not can save');
				}
			}
		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 */
		protected function _saveManyBefore(Record $record, Relationship $relationship = null, array $processed = [], array $changed = null){
			$relationship->beforeHolderSave($changed);
		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Field
		 * @throws Record\Exception\Field\AccessViolation
		 * @throws Record\Exception\Field\ReadonlyViolation
		 * @throws Record\Exception\Field\UnexpectedValue
		 * @throws Record\Exception\RelatedRecordError
		 */
		protected function _saveManyAfter(Record $record, Relationship $relationship = null, array $processed = [], array $changed = null){
			$relationship->afterHolderSave($changed);
			if($relationship){
				if($items = $relationship->getDirtyAddedItems()){
					foreach($items as $item){
						foreach($this->referenced_fields as $i => $relationFieldName){
							$item->setProperty($relationFieldName, $record->getProperty($this->fields[$i]));
						}
						if(!$item->save()){
							throw new Record\Exception\RelatedRecordError('Related[MANY] records in "' . $this->name . '" is not can save');
						}
					}
				}
				if($items = $relationship->getDirtyRemovedItems()){
					$rSchema = $relationship->getSchema();
					$opposites = $rSchema->getOppositeRelationsFor($this);
					foreach($items as $item){

						if($item->getOperationMade() === Record::OP_CREATE){
							continue;
						}

						$toRemove = false;
						foreach($opposites as $i => $oppositeField){
							if($oppositeField->isNullable()){
								$item->setProperty($oppositeField->getName(), null);
							}else{
								$toRemove = true;
								break;
							}
						}
						if($toRemove){
							if(!$item->delete()){
								throw new Record\Exception\RelatedRecordError('Excluded related[MANY] records in "' . $this->name . '" is not can save');
							}
						}else{
							if(!$item->save()){
								throw new Record\Exception\RelatedRecordError('Excluded Related[MANY] records in "' . $this->name . '" is not can save');
							}
						}

					}
				}
				$relationship->resetDirty();
			}
		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\RelatedRecordError
		 */
		protected function _saveManyThroughBefore(Record $record, Relationship $relationship, array $processed = [], array $changed = null){
			$relationship->beforeHolderSave($changed);
			if($items = $relationship->getDirtyAddedItems()){
				foreach($items as $item){
					if($item->getOperationMade() === Record::OP_CREATE){
						if(!$item->save()){
							throw new Record\Exception\RelatedRecordError('Related[MANY-THROUGH] records in "' . $this->name . '" is not can save');
						}
					}
				}
			}
		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Field
		 * @throws Record\Exception\Field\AccessViolation
		 * @throws Record\Exception\Field\ReadonlyViolation
		 * @throws Record\Exception\Field\UnexpectedValue
		 * @throws Record\Exception\RelatedRecordError
		 * @throws \Exception
		 */
		protected function _saveManyThroughAfter(Record $record, Relationship $relationship, array $processed = [], array $changed = null){
			$relationship->afterHolderSave($changed);
			if($items = $relationship->getDirtyAddedItems()){
				$iSchema = $this->schema->getSchemaManager()->getSchema($this->getIntermediateSchema());
				$iCollection = $iSchema->getCollection();
				/** @var \Jungle\Data\Record $item */
				foreach($items as $item){
					$iRecord = $iSchema->initializeRecord();
					foreach($this->referenced_fields as $i => $relationFieldName){
						$iRecord->setProperty($this->intermediate_referenced_fields[$i], $item->getProperty($relationFieldName));
					}
					foreach($this->fields as $i => $relationFieldName){
						$iRecord->setProperty($this->intermediate_fields[$i], $record->getProperty($relationFieldName));
					}
					if(!$iRecord->save()){
						throw new Record\Exception\RelatedRecordError('Intermediate Link for Related[MANY-THROUGH] record in "' . $this->name . '" is not can save');
					}
					$iRecord->markRecordInitialized();
					$relationship->setIntermediate($item, $iRecord);
					$iCollection->add($iRecord);
				}
			}
			$relationship->resetDirty();
		}

		/**
		 *
		 *
		 * TODO Обновлять Changed Массив, после предварительной обработки Related объектов, т.к значения могут быть измененными изза ForeignKeys
		 *
		 */
	}
}

