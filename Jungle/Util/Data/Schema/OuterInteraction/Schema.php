<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:07
 */
namespace Jungle\Util\Data\Schema\OuterInteraction {

	use Jungle\Util\Data\Schema\Indexed\SchemaInterface as IndexedSchemaInterface;
	use Jungle\Util\Data\Schema\SchemaInterface;


	/**
	 * @Outer-Interaction
	 * Class SchemaOuterInteraction
	 * @package modelX
	 *
	 * @property Field[]    $fields
	 *
	 * @method Field[]      getFields()
	 * @method Field        getPrimaryField()
	 */
	abstract class Schema
		extends \Jungle\Util\Data\Schema\Schema
		implements SchemaInterface,
		IndexedSchemaInterface,
		ValueAccessAwareInterface{

		/** @var array  */
		protected $default_original_data = [];


		/**
		 * @return array
		 */
		public function getDefaultOriginalData(){
			return $this->default_original_data;
		}

		/**
		 * @param array $original_data
		 */
		public function setDefaultOriginalData($original_data = [ ]){
			$this->default_original_data = $original_data;
		}

		/**
		 * @param $field
		 */
		protected function beforeAddField($field){
			if(!$field instanceof Field){
				throw new \LogicException();
			}
		}

		/**
		 * @param $name
		 * @return Field|null
		 */
		public function getField($name){
			foreach($this->fields as $field){
				if($field->valueAccessExists($name)){
					return $field;
				}
			}
			return null;
		}

		/**
		 * @param $data
		 * @param $key
		 * @return mixed
		 */
		public function valueAccessGet($data, $key){
			$field = $this->getField($key);
			if($field){
				return $field->valueAccessGet($data, $key);
			}else{
				throw new \LogicException('Field "' . $key . '" not found');
			}
		}

		/**
		 * @param $data
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $key, $value){
			if(!$data){
				$data = $this->default_original_data;
			}
			$field = $this->getField($key);
			if($field){
				return $field->valueAccessSet($data, $key, $value);
			}else{
				throw new \LogicException('Field "' . $key . '" not found');
			}
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function valueAccessExists($key){
			return !!$this->getField($key);
		}


	}

}

