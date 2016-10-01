<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 12.03.2015
 * Time: 0:03
 */

namespace Jungle\Util\Smart\Keyword;

use Jungle\Util\NamedInterface;
use Jungle\Util\Smart\Keyword\Storage;
use Jungle\Util\TransientInterface;

/**
 * Class Pool
 * @package Jungle\Util\Smart\Keyword
 */
abstract class Pool implements TransientInterface , NamedInterface{

	/** @var  string */
	protected $alias;

	/** @var  bool */
	protected $dirty;

	/** @var  bool */
	protected $dummy_allowed;

	/** @var  bool */
	protected $case_insensitive;

	/** @var  Storage */
	protected $storage;

	/** @var  Factory */
	protected $factory;

	/** @var  Keyword[] */
	protected $pool = [];

	/** @var  bool */
	protected $autoload = false;

	/** @var  bool */
	protected $full_loaded = false;


	/** @var  Pool */
	protected $parent;

	/** @var  Manager */
	protected $manager;

	/**
	 * @param Pool $manager
	 */
	public function setParent(Pool $manager){
		$this->parent = $manager;
	}

	/**
	 * @return Pool
	 */
	public function getParent(){
		return $this->parent;
	}

	/**
	 * @return Manager
	 */
	public function getManager(){
		return $this->manager;
	}

	/**
	 * @param Manager|null $context
	 * @param bool|false $appliedInNew
	 * @param bool|false $appliedInOld
	 * @return $this
	 */
	public function setManager(Manager $context = null, $appliedInNew = false, $appliedInOld = false){
		$old = $this->manager;
		if($old !== $context){
			$this->manager = $context;
			if($context && !$appliedInNew){
				$context->addPool($this,true);
			}
			if($old && !$appliedInOld){
				$old->removePool($this,true);
			}
		}
		return $this;
	}

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
	 * @return $this
	 */
	public function setStorage(Storage $storage){
		if($this->storage!==$storage){
			$this->storage = $storage;
		}
		return $this;
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
	 * @return $this
	 */
	public function dummySetAllowed($allowed = true){
		$this->dummy_allowed = $allowed===true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function dummyIsAllowed(){
		return $this->storage instanceof Storage\Dummy || $this->dummy_allowed===true;
	}

	/**
	 * @param bool $insensitive
	 * @return $this
	 */
	public function caseSetInsensitive($insensitive = true){
		$this->case_insensitive = $insensitive===true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function caseIsInsensitive(){
		return $this->case_insensitive===true;
	}

	/**
	 * @param bool $state
	 * @return $this
	 */
	public function setDirty($state = true){
		$this->dirty = $state===true;
		return $this;
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
		$key->setPool($this);

		if($key->isDirty()){
			$this->setDirty(true);
		}

	}

	/**
	 * @param string $identifier
	 * Identifier is general search parameter for KeyPool
	 * @return Keyword
	 */
	public function get($identifier){
		if($identifier instanceof Keyword){
			$identifier = $identifier->getIdentifier();
		}
		if($this->isAutoload()){
			if(!$this->full_loaded){
				$this->full();
				$this->full_loaded = true;
			}
		}
		$i = $this->search($identifier);
		if($i!==false){
			$key = $this->pool[$i];
		}elseif($this->parent){
			$key = $this->parent->get($identifier);
		}else{
			$key = null;
			$store = $this->getStorage();
			if($store->has($identifier)){
				$key = $store->load($identifier);
				$key->setDirty(false);
			}elseif($this->dummyIsAllowed()){
				$key = $this->create($identifier);
				$key->setDummy();
			}else{
				throw new \LogicException('Key of identifier: "'.$identifier.'" not found.');
			}
			$this->push($key);
		}
		return $key;
	}

	/**
	 * @param bool|true $autoload
	 * @return $this
	 */
	public function setAutoload($autoload = true){
		$this->autoload = (bool)$autoload;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAutoload(){
		return $this->autoload;
	}

	/**
	 * @return Keyword[]
	 */
	public function getKeywords(){
		return $this->full()->pool;
	}

	/**
	 *
	 */
	protected function full(){
		$store = $this->getStorage();
		$list = $store->getList();
		foreach($list as $id){
			$key = $store->load($id);
			if($key){
				$key->setDirty(false);
				if($this->search($key->getIdentifier())===false){
					$this->pool[] = $key;
				}
				$key->setPool($this);
			}
		}
		return $this;
	}



	/** Search key in pool
	 * @param string $identifier
	 * @return bool|int|string
	 */
	public function search($identifier){
		$fn = $this->caseIsInsensitive()?'strcasecmp':'strcmp';
		foreach($this->pool as $i => & $key){
			if($this->compareIdentifiers($identifier,$key->getIdentifier(),$fn)){
				return $i;
			}
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

	/**
	 * @param $identifier
	 * @return bool
	 */
	public function exists($identifier){
		return $this->has($identifier) || $this->getStorage()->has($identifier);
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
				$key->setPool(null);
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
			$this->full_loaded = false;
		}
	}

	/**
	 * Auto commit
	 */
	public function __destruct(){
		$this->commit();
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->getAlias();
	}

	/**
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->setAlias($name);
		return $this;
	}

	/**
	 * @param $identifier
	 * @return Keyword
	 */
	protected function create($identifier){
		return $this->getFactory()->create($identifier);
	}

}