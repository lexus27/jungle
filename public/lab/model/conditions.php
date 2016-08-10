<?php


/**
 * Interface ConditionInterface
 */
interface ConditionInterface{

	/**
	 * @param $data
	 * @param null $access
	 * @return bool
	 */
	public function __invoke($data, $access = null);

}

/**
 * Class ContextSchema
 */
class ContextSchema{

	/**
	 * @var array
	 */
	protected $schemas = [];

	/**
	 * @var array
	 */
	protected $schema_aliases = [];

	/**
	 * @param $schema
	 */
	public function addSchema($schema){
		$name = $schema->getName();
		$this->schemas[$name] = $schema;
		$this->schema_aliases[$name] = $name;
	}

	/**
	 * @param $schema
	 * @return bool
	 */
	public function hasSchema($schema){
		if(!is_string($schema))$schema = $schema->getName();
		return isset($this->schema_aliases[$schema]);
	}

	/**
	 * @param $schema
	 * @return null
	 */
	public function getSchema($schema){
		if(!is_string($schema))$schema = $schema->getName();
		if(isset($this->schema_aliases[$schema])){
			return $this->schemas[$this->schema_aliases[$schema]];
		}
		return null;
	}

	/**
	 * @param $schema
	 * @param ...$aliases
	 * @return $this
	 */
	public function setSchemaAlias($schema, ...$aliases){
		if(!is_string($schema))$schema = $schema->getName();
		if(isset($this->schemas[$schema])){
			foreach($aliases as $alias){
				$this->schema_aliases[$alias] = $schema;
			}
		}
		return $this;
	}

	/**
	 * @param $schema
	 * @param ...$aliases
	 * @return $this
	 */
	public function removeSchemaAlias($schema, ...$aliases){
		if(!is_string($schema))$schema = $schema->getName();
		if(isset($this->schema_aliases[$schema])){
			foreach($aliases as $alias){
				unset($this->schema_aliases[$alias]);
			}
		}
		return $this;
	}

}

class Condition{

	protected $schemas = [];

	/**
	 * @param $schema
	 * @param null $as
	 */
	public function setSchema($schema, $as = null){
		$this->schemas[($as?$as:$schema->getName())] = $schema;
	}

}