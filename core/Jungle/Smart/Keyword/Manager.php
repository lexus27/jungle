<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 12.03.2015
 * Time: 0:03
 */

namespace Jungle\Smart\Keyword;

use Jungle\Basic\ITransient;
use Jungle\Smart\Keyword\Storage;

/**
 * Class Manager
 * @package Jungle\Smart\Keyword
 */
abstract class Manager implements ITransient {

	/**
	 * @var string
	 */
	protected $alias;

	/**
	 * @var bool
	 */
	protected $dirty;

	/**
	 * @var bool
	 */
	protected $dummy_allowed;

	/**
	 * @var bool
	 */
	protected $case_insensitive;

	/**
	 * @var Storage
	 */
	protected $storage;

	/**
	 * @var Factory
	 */
	protected $factory;

	/**
	 * @var Keyword[]
	 */
	protected $pool = [];

	/**
	 * @param $alias
	 * @param Storage $storage
	 */
	public function __construct($alias,Storage $storage){
		$this->setAlias($alias);
		$this->setStorage($storage);
	}

	/**
	 * @param Factory $factory
	 */
	public function setFactory(Factory $factory){
		if($this->factory!==$factory){
			$this->factory = $factory;
		}
	}

	/**
	 * @return Factory
	 */
	public function getFactory(){
		return $this->factory;
	}

	/**
	 * @param string $alias
	 */
	private function setAlias($alias){
		if(!$alias){
			throw new \LogicException('KeyPool alias required to long');
		}
		$this->alias = strtolower($alias);
	}

	/**
	 * @return string
	 */
	public function getAlias(){
		return $this->alias;
	}


	/**
	 * @param Storage $storage
	 */
	public function setStorage(Storage $storage){
		if($this->storage!==$storage){
			$this->storage = $storage;
		}
	}

	/**
	 * @return Storage
	 */
	public function getStorage(){
		if($this->storage){
			$this->storage->setManager($this);
		}
		return $this->storage;
	}

	/**
	 * @param bool $allowed
	 */
	public function dummySetAllowed($allowed = true){
		$this->dummy_allowed = $allowed===true;
	}

	/**
	 * @return bool
	 */
	public function dummyIsAllowed(){
		return $this->storage instanceof Storage\Dummy || $this->dummy_allowed===true;
	}

	/**
	 * @param bool $insensitive
	 */
	public function caseSetInsensitive($insensitive = true){
		$this->case_insensitive = $insensitive===true;
	}

	/**
	 * @return bool
	 */
	public function caseIsInsensitive(){
		return $this->case_insensitive===true;
	}

	/**
	 * @param bool $state
	 */
	public function setDirty($state = true){
		$this->dirty = $state===true;
	}

	/**
	 * @return bool
	 */
	public function isDirty(){
		return $this->storage instanceof Storage\Dummy?false:$this->dirty===true;
	}

	/**
	 * @param string $identifier1
	 * @param string $identifier2
	 * @param string $func (strcasecmp or strcmp and them)
	 * @return bool
	 */
	public function compareIdentifiers($identifier1,$identifier2,$func=null){
		if(!$func)$func = $this->caseIsInsensitive()?'strcasecmp':'strcmp';
		return call_user_func($func,$identifier1,$identifier2)===0;
	}

	/**
	 * @param Keyword $key
	 */
	public function push(Keyword $key){
		$i = $this->search($key->getIdentifier());
		if($i!==false){
			$this->pool[$i] = $key;
		}else{
			$this->pool[] = $key;
		}
		$key->setManager($this);

		if($key->isDirty()){
			$this->setDirty(true);
		}

	}

	/**
	 * @param string $identifier
	 * @return Keyword
	 */
	public function get($identifier){
		if($identifier instanceof Keyword){
			$identifier = $identifier->getIdentifier();
		}
		$i = $this->search($identifier);
		if($i!==false){
			$key = $this->pool[$i];
		}else{
			$key = null;
			$store = $this->getStorage();
			if($store->has($identifier)){
				$key = $store->load($identifier);
				$key->setDirty(false);
			}elseif($this->dummyIsAllowed()){
				$key = $this->getFactory()->create($identifier);
				$key->setDummy();
			}else{
				throw new \LogicException('Key of identifier: "'.$identifier.'" not found.');
			}
			$this->push($key);
		}
		return $key;
	}

	/** Search key in pool
	 * @param string $identifier
	 * @return bool|int|string
	 */
	public function search($identifier){
		$fn = $this->caseIsInsensitive()?'strcasecmp':'strcmp';
		foreach($this->pool as $i => & $key){
			if($this->compareIdentifiers($identifier,$key->getIdentifier(),$fn))return $i;
		}
		return false;
	}

	/** Find instance by identifier or false
	 * @param $identifier
	 * @return bool|Keyword
	 */
	public function find($identifier){
		$i = $this->search($identifier);
		if($i!==false){
			return $this->pool[$i];
		}
		return false;
	}

	/** If has key in pool(not store check)
	 * @param string $identifier
	 * @return bool
	 */
	public function has($identifier){
		return $this->search($identifier)!==false;
	}

	/** Remove key from pool and store
	 * @param string $identifier
	 */
	public function remove($identifier){
		$i = $this->search($identifier);
		if($i!==false){
			$key = $this->pool[$i];
			if($this->getStorage()->remove($identifier)){
				array_splice($this->pool,$i,1);
				$key->setManager(null);
				unset($key);
			}
		}
	}

	/**
	 * Commit all changes for each Keyword instance in pool collection if have dirty state
	 */
	public function commit(){
		if($this->isDirty()){
			foreach($this->pool as & $key){
				if(!$key->isDummy() && $key->isDirty()){
					$this->getStorage()->save($key);
					$key->setDirty(false);
				}
			}
			$this->setDirty(false);
		}
	}

	/**
	 * Auto commit
	 */
	public function __destruct(){
		$this->commit();
	}
}