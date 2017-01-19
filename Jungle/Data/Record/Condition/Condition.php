<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.01.2017
 * Time: 23:51
 */
namespace Jungle\Data\Record\Condition {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Relation\RelationForeign;
	use Jungle\Data\Record\Relation\RelationSchema;
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\Util\Value\Massive;
	use Jungle\Util\Value\String;

	class Condition{

		const TYPE_KEY = '::type';
		const TYPE_BLOCK = '(...)';


		/** @var Schema */
		public $base;
		/** @var Schema[] */
		public $context = [];

		public $aliases = [];

		public $paths = [];

		public $columns = [];

		public $where = [];

		public $table;

		public $alias;

		public $joins = [];


		public $left;

		public $operator;

		public $right;

		public $many = [];


		public function prepare(Schema $schema){


			$schema_name = $schema->getName();
			$alias = $schema_name;
			if(($alias_pos = strrpos($schema_name,'\\'))!==false){
				$alias = strtolower(substr($schema_name,$alias_pos+1));
			}
			$this->alias = $alias;
			$this->base = $schema;

			/**
			 * Нужны:
			 * Джоины для склеивания связей в линию
			 * Вложенные запросы для COUNT, IN, для вхождения в Many отношения
			 */

			/**
			 *
			 * Вхождение в связанную коллекцию : Разбор полетов
			 *
			 * Зачем входить в коллекцию?
			 * Чтобы узнать есть ли в коллекции записи по условию (HAVE) в колличестве > 0 AND < 5
			 * Чтобы узнать нет ли в коллекции записей по условию (NOT HAVE) в колличествах > 0 AND < 5
			 * Чтобы сделать условие по колличеству записей в этой коллекции
			 * Чтобы Сделать GROUP_CONCAT какого-то поля каждого объекта(Аналогия Collection::listProperty($property_name))
			 *
			 */
			/**
			 * 2 типа вхождения в множественные связи.
			 * Использование LEFT JOIN
			 *
			 * For Preliminary optimization
			 * $scope = $record->getLoadedScope();
			 * $scope['profile.first_name'];
			 * $scope['notes:count'];
			 * $scope['notes:sum'];
			 */
			$condition = [
				['{profile.first_name}', 'LIKE', '%beef%'], // означает что в запрос подставиться JOIN так как профиль, присоединяется линейно
				['{username}','=',NULL], // username IS NULL,
				['{notes:count}','>',5],
				['{notes}','HAVE',[
					'each'      => 'note',
					'count'     => ['> 5','<= 1'],
					'average'   => '',
					'sum'       => '',
					'max'       => '',
					'min'       => '',
					'concat'    => [''],
					'aggregations' => [
						['concat','{id}'],
						['min',[
							['@value', '>', 5], 'OR',
							['@value', '<', 30]
						]],
					],
					'condition' => [
						['{title}','LIKE','%john%'],
						['{editor.id}','=','{user.id}'] // если связи FK то сравнить используя локальные поля // если используется объект то подставить его PK или отражение локальных FK
					]
				]],
			];

			$includes = [

				'profile',
				'members',
				'members' => [
					'average' => '{}'
				],

			];

			$sort_by = [
				'profile.first_name' => 'ASC'
			];

			$usages = [];


			$select_query = [
				'schema' => 'App/Model/User',
				'include' => [
					'profile',
					'members:count'
				],
				'condition' => [
					['{profile.first_name}', 'LIKE', '%beef%']
				],
				'sort' => [
					'profile.first_name' => 'ASC'
				]
			];

			$result_query = [
				'table'     => 'ex_user',
				'alias'     => 'u',
				'columns'   => ['u.username','p.first_name','m_count','....','....'],
				'where'     => [
					['p.first_name','LIKE','%beef%'],
					['u.id','=','p.id']
				],
				'joins'     => [[
					'type'      => 'type',
					'table'     => '',
					'alias'     => 'p',
					'on'        => [
						['u.id','=','p.id']
					]
				]],
			];


			// Выборка записей по условиям с использованием связанных записей!!

			// Выборка записей с захватом данных для связанных путей!! |=   SELECT &, profile, {notes}


			$this->where = $this->handleConditionBlock($condition);


		}

		public function handleMany($path, $operator, $value){

		}

		public function handleConditionBlock(array $block){
			$a = [];$b = false;
			foreach($block as $condition){
				if(is_array($condition)){
					if($condition && ($cc = $this->handleConditionArray($condition))){
						if($b){
							$a[] = 'AND';
						}
						$b = true;
						$a[] = $cc;
					}
				}else{
					$b = false;
					$a[] = $condition?:'AND';
				}
			}
			return $a;
		}




		/**
		 * @param array $condition
		 * @return null|void
		 */
		public function handleConditionArray(array $condition){
			if(isset($condition[self::TYPE_KEY])){
				if($condition[self::TYPE_KEY] === 'block'){
					unset($condition[self::TYPE_KEY]);
					return $this->handleConditionBlock($condition);
				}else{
					unset($condition[self::TYPE_KEY]);
				}
			}elseif(isset($condition[0]) && is_string($condition[0]) && $condition[0]===self::TYPE_BLOCK){
				array_shift($condition);
				return $this->handleConditionBlock($condition);
			}else{
				if(Massive::isAssoc($condition, true)){
					$a = [];
					foreach($condition as $k => $v){
						$a[] = ['{'.$k.'}','=',$v];
					}
					return $this->handleConditionBlock($a);
				}else{
					list($left, $operator, $right) = array_replace([null,null,null],$condition);
					return $this->handleCondition($left, $operator, $right);
				}
			}
			return null;
		}

		public function handleCondition($left, $operator, $right){
			$this->left     = $left;
			$this->operator = $operator;
			$this->right    = $right;


			if(($have = strcasecmp($operator,'have')===0) || ($nHave = strcasecmp($operator,'not have')===0)){

				// $right - Данные для Подзапроса
				// $left ссылка на множественное отношение
				if(String::isCovered($this->left,'{','}')){
					$this->left = String::trimSides($this->left,'{','}');
					$extra = null;
					if(($pos = strpos($this->left,':')) !== false){
						$extra = substr($this->left, $pos+1);
						$this->left = substr($this->left, 0,$pos);
					}
					$alias = $this->match_path($this->left,$extra);
					if(isset($this->many[$alias])){
						/**
						 * @var Schema $prev_schema
						 * @var Schema $schema
						 * @var RelationSchema $relation
						 */
						$point = $this->many[$alias];
						$prev_alias = $point['prev_alias'];
						$prev_schema = $point['prev_schema'];
						$relation = $point['relation'];
						$schema = $point['schema'];
						$this->left = $this->toDataBaseIdentifier($prev_alias.'.'.$prev_schema->getOriginal($relation->fields[0]));
						$this->operator = strtr(strtolower($operator),['have'=>'in']);
						$this->right = clone $this;
						$this->right->base = $schema;
						$this->right->alias = $right['each']?:$alias;
						$this->right->columns = [$schema->getOriginal($relation->referenced_fields[0])];
						$this->right->paths = [];
						$this->right->aliases[$this->alias] = $this->base;
						$this->right->joins = [];
						$this->right->many = [];
						$this->right->where = $this->right->handleConditionBlock(array_merge($point['condition'],$right['condition']));
					}
				}
			}else{
				if(String::isCovered($this->left,'{','}')){
					$this->left = String::trimSides($this->left,'{','}');
					$this->left = $this->handlePathLeft($this->left);
				}
				if($this->operator){
					$this->operator = $this->handleOperator($this->operator);
					if($this->right && is_string($this->right)){
						if(String::isCovered($this->right,'{','}')){
							$this->right = String::trimSides($this->right,'{','}');
							$this->right = $this->handlePathRight($this->right);
						}
					}
				}
			}
			return [$this->left, $this->operator,$this->right];
		}

		public function toDataBaseIdentifier($identifier){
			return ['identifier' => $identifier];
		}

		public function handlePathLeft($path){
			return $this->handlePath($path);
		}

		public function handlePathRight($path){
			return $this->handlePath($path);
		}

		public function handleOperator($operator){
			if($operator === '=' && $this->right === null){
				return 'IS NULL';
			}elseif($operator === '!=' && $this->right === null){
				return 'IS NOT NULL';
			}
			return $operator;
		}

		public function handlePath($path){
			$extra = null;
			if(($pos = strpos($path,':')) !== false){
				$extra = substr($path, $pos+1);
				$path = substr($path, 0,$pos);
			}
			if(!isset($this->paths[$path])){
				$identifier = $this->match_path($path,$extra);
				$this->paths[$path] = $identifier;
				return [ 'identifier' => $identifier ];
			}
			return [ 'identifier' => $this->paths[$path] ];
		}


		public function match_path($path, $extra = null){
			/** @var Schema $schema */
			$path = $this->base->analyzePathPoints($path);

			if($path['field']){

				$container_alias = $this->alias;
				$field = $path['field'];
				//$field_original = $schema->getOriginal($field);
				if($path['points'] && !$path['circular']){

					if(isset($path['aggregate'][0]) && $path['aggregate'][0]){
						// many HAVE {each.name} = 'lisee'
						// Подзапрос в котором доступны идентификаторы Текущего запроса, например для Условий сопоставления
						return $this->handlePointsForMany($path['points']);
					}else{
						// single (Легко Джоинятся и соединяются в линию)
						$container_alias = $this->handlePoints($path['points']);
					}

					// remote field
				}else{
					// local field
				}
				return $container_alias.'.'.$path['schema']->getOriginal($field);
			}else{
				// not a field(Для указания зависимостей от связанных схем)

				if($path['aggregate'][0]){
					// many HAVE {each.name} = 'lisee'
					// Подзапрос в котором доступны идентификаторы Текущего запроса, например для Условий сопоставления
					return $this->handlePointsForMany($path['points']);
				}

			}




		}

		public function handlePoints(array $points){
			/** @var Schema $prev_schema */
			$prev_schema = $this->base;
			$prev_alias = $this->alias;
			foreach($points as $point){
				/**
				 * @var Schema $schema
				 * @var RelationForeign $relation
				 */
				$schema = $point['schema'];
				$relation = $point['relation'];
				$alias = $point['elapsed'];
				if(!isset($this->aliases[$alias])){
					$this->handleAlias($schema, $alias, $relation, $prev_schema, $prev_alias);
				}
				$prev_schema = $schema;
				$prev_alias = $alias;
			}
			return $prev_alias;
		}

		public function handlePointsForMany(array $points){
			/** @var Schema $prev_schema */
			$prev_schema = $this->base;
			$prev_alias = $this->alias;
			$c = count($points);$i = 0;
			foreach($points as $point){
				/**
				 * @var Schema $schema
				 * @var RelationForeign $relation
				 */
				$schema = $point['schema'];
				$relation = $point['relation'];
				$alias = $point['elapsed'];
				if(!isset($this->aliases[$alias])){
					$condition = [];

					$table_name = $schema->getDefaultSource();
					$this->aliases[$alias]       = $table_name;
					$this->context[$table_name]  = $schema;
					if($i+1<$c){// если не множественная
						foreach($relation->fields as $i => $field_name){
							$ref_field = $relation->referenced_fields[$i];
							$condition[] = [
								$prev_alias.'.'.$prev_schema->getOriginal($field_name),
								'=',
								$alias.'.'.$schema->getOriginal($ref_field)
							];
						}
						$this->joins[$alias] = [
							'table' => $table_name,
							'alias' => $alias,
							'on'    => $condition
						];
					}else{
						foreach($relation->fields as $i => $field_name){
							$ref_field = $relation->referenced_fields[$i];
							$condition[] = [
								$this->toDataBaseIdentifier($prev_alias.'.'.$prev_schema->getOriginal($field_name)),
								'=',
								String::cover($schema->getOriginal($ref_field),'{','}')
							];
						}
						$this->many[$alias] = array_replace($point,[
							'prev_alias' => $prev_alias,
							'prev_schema' => $prev_schema,
							'condition' => $condition
						]);
					}
				}else{
					if(isset($this->many[$alias])){

					}
				}
				$prev_schema = $schema;
				$prev_alias = $alias;
				$i++;
			}
			return $prev_alias;
		}


		/**
		 * @param Schema $schema
		 * @param $alias
		 * @param Record\Relation\RelationSchema $relation
		 * @param Schema $prev_schema
		 * @param $prev_alias
		 * @throws \Exception
		 */
		public function handleAlias(Schema $schema, $alias, Record\Relation\RelationSchema $relation, Schema $prev_schema, $prev_alias){
			$condition = [];
			foreach($relation->fields as $i => $field_name){
				$ref_field = $relation->referenced_fields[$i];
				$condition[] = [
					$prev_alias.'.'.$prev_schema->getOriginal($field_name),
					'=',
					$alias.'.'.$schema->getOriginal($ref_field)
				];
			}
			$table_name = $schema->getDefaultSource();
			$this->joins[$alias] = [
				'table' => $table_name,
				'alias' => $alias,
				'on'    => $condition
			];
			$this->aliases[$alias]       = $table_name;
			$this->context[$table_name]  = $schema;
		}

	}
}

