<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.06.2016
 * Time: 20:04
 */
namespace Jungle\Data\Storage\Db {

	/**
	 * Class ConditionTarget
	 * @package Jungle\Data\Storage\Db
	 */
	abstract class ConditionTarget{

		/** @var  array|string */
		protected $identifier;

		/**
		 * ConditionTarget constructor.
		 * @param $identifier
		 */
		public function __construct($identifier){
			$this->identifier = $identifier;
		}

		/**
		 * @return array|string
		 */
		public function getIdentifier(){
			return $this->identifier;
		}

		/**
		 * @param Dialect $dialect
		 * @param Sql $servant
		 * @return bool
		 */
		abstract public function mountIn(Dialect $dialect, Sql $servant);

	}

}

