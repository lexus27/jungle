<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 12.03.2015
 * Time: 0:03
 */

namespace Jungle\Smart\Keyword;

use Jungle\Smart\Keyword\Storage\Dummy as DummyStorage;

/**
 * Class Storage
 * @package Jungle\Smart\Keyword
 */
abstract class Storage {

	/** @var  Pool */
	protected $manager;

	protected $config;

	/**
	 * @var DummyStorage
	 */
	protected static $dummy;

	/**
	 * @return DummyStorage
	 */
	public static function getDummy(){
		if(!self::$dummy instanceof DummyStorage){
			self::$dummy = new DummyStorage;
		}
		return self::$dummy;
	}

	/**
	 * @param array $config
	 */
	public function __construct(array $config = []){
		$this->config = $config;
	}

	/**
	 * @param Pool $manager
	 */
	public function setManager(Pool $manager){
		if($this->manager!==$manager){
			$this->manager = $manager;
			$manager->setStorage($this);
		}
	}

	/**
	 * @return Pool
	 */
	public function getManager(){
		return $this->manager;
	}

	/**
	 * @param Keyword $key
	 */
	abstract public function save(Keyword $key);

	/**
	 * @param $identifier
	 * @return Keyword
	 */
	abstract public function load($identifier);

	/**
	 * @TODO $matcher CLASS MATCHER
	 * @param $matcher
	 * @return array identifiers
	 */
	abstract public function getList($matcher = null);

	/**
	 * @TODO $matcher CLASS MATCHER
	 * @param $matcher
	 * @return mixed
	 */
	abstract public function getCount($matcher=null);

	/**
	 * @param $identifier
	 * @return bool
	 */
	abstract public function has($identifier);

	/**
	 * @param $identifier
	 * @return bool
	 */
	abstract public function remove($identifier);
}