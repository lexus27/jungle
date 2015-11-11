<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 12.03.2015
 * Time: 0:03
 */

namespace Jungle\Smart\Keyword;

use Jungle\Smart\Keyword\Storage\Dummy as DummyStorage;

abstract class Storage {

	/** @var  Manager */
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
	 * @param Manager $manager
	 */
	public function setManager(Manager $manager){
		if($this->manager!==$manager){
			$this->manager = $manager;
			$manager->setStorage($this);
		}
	}

	/**
	 * @return Manager
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