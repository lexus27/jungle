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
	use Jungle\Util\Data\Foundation\Schema\FieldVisibilityControlInterface;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\Mapped\Field as MappedField;

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

		/** @var  bool  */
		protected $readonly = false;

		/** @var  bool  */
		protected $private = false;

		/** @var bool  */
		protected $enumerable = true;

		/**
		 * @param $type
		 * @return $this
		 */
		public function setType($type){
			$this->type = $type;
			return $this;
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

	}
}

