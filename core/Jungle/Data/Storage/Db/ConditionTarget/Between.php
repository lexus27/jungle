<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.06.2016
 * Time: 20:10
 */
namespace Jungle\Data\Storage\Db\ConditionTarget {
	
	use Jungle\Data\Storage\Db\ConditionTarget;
	use Jungle\Data\Storage\Db\Dialect;
	use Jungle\Data\Storage\Db\Sql;
	use Jungle\Data\Storage\Db\Structure\Column;

	/**
	 * Class Between
	 * @package Jungle\Data\Storage\Db\ConditionTarget
	 */
	class Between extends ConditionTarget{


		protected $min;

		protected $max;

		public function __construct($identifier, $min, $max){
			parent::__construct($identifier);
			$this->min = $min;
			$this->max = $max;
		}

		/**
		 * @param Dialect $dialect
		 * @param Sql $servant
		 * @return bool
		 */
		public function mountIn(Dialect $dialect, Sql $servant){
			$binds = [];
			$types = [];
			$servant->push(
				$dialect->escape($this->identifier) .
				' BETWEEN '.
				$servant->valueCaptureToSql($this->min,$this->identifier.'_min',$binds,$types).
				' AND '.
				$servant->valueCaptureToSql($this->max,$this->identifier.'_max',$binds,$types),
				' ',$binds,$types);
			return true;
		}


	}
}

