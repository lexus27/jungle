<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.06.2016
 * Time: 20:11
 */
namespace Jungle\Data\Storage\Db\ConditionTarget {
	
	use Jungle\Data\Storage\Db\ConditionTarget;
	use Jungle\Data\Storage\Db\Dialect;
	use Jungle\Data\Storage\Db\Sql;
	
	/**
	 * Class Clean
	 * @package Jungle\Data\Storage\Db\ConditionTarget
	 */
	class Clean extends ConditionTarget{

		public function __construct($sql){
			parent::__construct($sql);
		}
		
		/**
		 * @param Dialect $dialect
		 * @param Sql $servant
		 * @return array|string|void
		 */
		public function mountIn(Dialect $dialect, Sql $servant){
			$servant->push($this->identifier,' ');
			return true;
		}
		
		
	}
}

