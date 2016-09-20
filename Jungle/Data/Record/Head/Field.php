<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:27
 */
namespace Jungle\Data\Record\Head {

	use Jungle\Data\Record;
	use Jungle\Util\Data\Schema\FieldVisibilityControlInterface;
	use Jungle\Util\Data\Schema\OuterInteraction\Mapped\Field as MappedField;
	use Jungle\Util\Data\Schema\ValueType;
	use Jungle\Util\Data\Schema\ValueType\ValueTypePool;
	use Jungle\Util\Data\Validation\Message\AggregationMessageException;

	/**
	 * Class Field
	 * @package Jungle\Data\Record
	 *
	 *
	 * @property Schema $schema
	 * @method Schema getSchema()
	 */
	class Field extends MappedField implements FieldVisibilityControlInterface{

		/** @var  Schema */
		protected $schema;

		/** @var  ValueType */
		protected $type;

		/** @var   */
		protected $type_params;

		/** @var  bool  */
		protected $readonly = false;

		/** @var  bool  */
		protected $private = false;

		/** @var bool  */
		protected $enumerable = true;

		/**
		 * @param $type
		 * @return $this
		 * @throws Record\Exception
		 */
		public function setType($type){
			if(is_array($type)){
				if(isset($type['params'])){
					$this->type_params = $type['params'];
				}
				if(isset($type['type'])){
					$typeName = $type['type'];
					$type = ValueTypePool::getDefault()->getType($typeName);
					if(!$type){
						throw new Record\Exception('Type error: '.$typeName);
					}
					$this->type = $type;
				}
			}else{
				$typeName = $type;
				$type = ValueTypePool::getDefault()->getType($typeName);
				if(!$type){
					throw new Record\Exception('Type error: '.$typeName);
				}
				$this->type = $type;
			}
			return $this;
		}

		/**
		 * @param $native_value
		 * @return mixed
		 */
		public function originate($native_value){
			if($native_value === null & $this->isNullable()){
				return null;
			}
			return $this->type->originate($native_value,$this->type_params);
		}

		/**
		 * @param $originality_value
		 * @return mixed
		 */
		public function evaluate($originality_value){
			return $this->type->evaluate($originality_value,$this->type_params);
		}

		/**
		 * @param $native_value
		 * @return bool
		 * @throws AggregationMessageException
		 */

		public function validate($native_value){
			if($native_value === null && $this->isNullable()){
				return true;
			}
			if(!$this->type->validate($native_value,$this->type_params)){
				$messages = $this->type->getLastMessages();
				throw new Record\Exception\Field\FieldAggregationMessageException($this, $messages);
			}
			return true;
		}

		/**
		 * @param $value
		 * @return mixed
		 */
		public function stabilize($value){
			if($value === null && $this->isNullable()){
				return $value;
			}
			return $this->type->stabilize($value,$this->type_params);
		}


		/**
		 * @param $readonly
		 * @return $this
		 */
		public function setReadonly($readonly){
			$this->readonly = $readonly;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isReadonly(){
			return $this->readonly;
		}

		/**
		 * @param $private
		 * @return $this
		 */
		public function setPrivate($private){
			$this->private = $private;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isPrivate(){
			return $this->private;
		}

		/**
		 * @return string
		 */
		public function getOriginalKey(){
			return $this->original_key?:$this->name;
		}

		/**
		 * @param bool|true $nullable
		 * @return $this
		 */
		public function setNullable($nullable = true){
			$this->nullable = $nullable;
			return $this;
		}

		/**
		 * @param $default
		 * @return $this
		 */
		public function setDefault($default){
			$this->default = $default;
			return $this;
		}

		/**
		 * TODO for technical internal use
		 */
		public function internal(){
			$this->enumerable = false;
			return $this;
		}

		/**
		 * @param \Jungle\Data\Record $data
		 * @param $key
		 * @return mixed
		 */
		public function valueAccessGet($data, $key){
			if(!$data){
				return $this->getDefault();
			}
			if($data instanceof Record){
				$original = $data->getOriginalData();
				if($data->getOperationMade() === $data::OP_CREATE && !$original){
					return $this->getDefault();
				}
				$data = $original;
			}
			return parent::valueAccessGet($data, $key);
		}

		/**
		 * @param Record $data
		 * @param $key
		 * @param $value
		 * @return mixed|null
		 */
		public function valueAccessSet($data, $key, $value){
			if($data instanceof Record){
				$data = $data->getOriginalData();
			}
			return parent::valueAccessSet($data, $key, $value);
		}

		/**
		 * @return bool
		 */
		public function isOriginality(){
			return !!$this->original_key || (!$this instanceof Field\Relation && !$this instanceof Field\Virtual);
		}

		/**
		 * @return bool
		 */
		public function isEnumerable(){
			return $this->enumerable && (!$this instanceof Field\Relation && !$this instanceof Field\Virtual);
		}


		public function beforeRecordSave(Record $record, array $processed, array $changed = null){

		}

		public function afterRecordSave(Record $record, array $processed, array $changed = null){

		}

		/**
		 * @param array $definition
		 */
		public function define(array $definition){
			$c = array_replace_recursive([
				'type' => 'string',
				'nullable' => false,
			    'default' => null,
			    'readonly' => false,
			    'private' => false,
			    'enumerable' => true,
			    'original_key' => null,
			    'changeable' => true,
			    'on_create' => function(){},
			    'on_update' => function(){}
			],$definition);unset($definition);
			if($c['type']){
				$this->setType($c['type']);
			}
			$this->setNullable($c['nullable']);
			$this->setDefault($c['default']);
			if(!$c['enumerable']) $this->internal();
			$this->setReadonly($c['readonly']);
			$this->setPrivate($c['private']);
			if($c['original_key']){
				$this->setOriginalKey($c['original_key']);
			}
		}

	}
}

