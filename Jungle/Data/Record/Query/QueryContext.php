<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.12.2016
 * Time: 1:02
 */
namespace Jungle\Data\Record\Query {

	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class QueryContext
	 * @package Jungle\Data\Record\Query
	 */
	class QueryContext{

		/** @var  Schema */
		public $main_schema;

		/** @var Schema[] */
		public $schemas = [];

		public $condition;


		/**
		 * @param $alias
		 * @return Schema|null
		 */
		public function getSchemaBy($alias = null){
			if(!$alias){
				return $this->main_schema;
			}else{
				return isset($this->schemas[$alias])?$this->schemas[$alias]:null;
			}
		}

		/**
		 * user.profile.id
		 * @param $identifier
		 * @param $container
		 * @param bool $trim
		 */
		public function normalize($identifier, $container = null, $trim = true){
			$pos = strpos(($trim?trim($identifier,'.'):$identifier), '.');
			if($pos!==false){
				$part = substr($identifier, 0, $pos-1);
				if($container === null){
					$container = $this->main_schema;
					if(isset($container->fields[$identifier])){
						return $this->normalize();
					}elseif($container->relations[$identifier]){

					}
				}elseif($container instanceof Schema){
					if(isset($container->fields[$part])){

					}elseif($container->relations[$part]){

					}
				}

			}else{

				if($container === null){
					$container = $this->main_schema;
					if(isset($container->fields[$identifier])){

					}elseif($container->relations[$identifier]){

					}
				}




			}
		}

		public function query(){

			$main = 'note';

			$paths_usage = [
				'comments' => 'comment',
				'uri' => null,
			];











		}


	}
}

