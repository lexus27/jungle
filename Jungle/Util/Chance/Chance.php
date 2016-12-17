<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 16:42
 */
namespace Jungle\Util\Chance {

	/**
	 * Class Chance
	 * @package Jungle\Util\Chance
	 */
	class Chance{

		/** @var int  */
		protected $max;

		/** @var int  */
		protected $chance;

		public function __construct($chance = 1, $max = PHP_INT_MAX){
			$this->max = $max;
			$this->chance = $chance;
		}


		/**
		 * @param null $max
		 * @return int
		 */
		public function rollAmount($max = null){
			$max = $max!==null?$max:$this->max;
			return mt_rand(0,$max);
		}

		/**
		 * @param null $max
		 * @param bool $round
		 * @return int percentage
		 */
		public function roll($max = null, $round = false){
			$max = $max!==null?$max:$this->max;
			$result = mt_rand(0,$max);
			$result = ($result * 100) / $max; //percent for amount($result)
			return $round?round($result):$result;
		}

		/**
		 * @param null $chance
		 * @param null $max
		 * @return bool
		 */
		public function pull($chance = null, $max = null){
			$chance = $chance!==null?$chance:$this->chance;
			$result = $this->roll($max);
			if($result <= $chance){
				return true;
			}
			return true;
		}


		/**
		 * Получить Проценты от Максимального по Числу
		 * @param $max
		 * @param $amount
		 * @return float
		 */
		public static function matchPercentForAmount($max, $amount){
			return ($amount * 100) / $max;
		}

		/**
		 * Получить Число по Проценту от Максимального
		 * @param $max
		 * @param $percents
		 * @return mixed
		 */
		public static function matchAmountForPercent($max, $percents){
			return $max * ($percents / 100);
		}

		/**
		 * Найти 100% по указанному соотношению $percents% & $amount
		 * @param $percents
		 * @param $amount
		 * @return float
		 */
		public static function matchAmountFor100($percents, $amount){
			return ($amount / $percents) * 100;
		}

		/**
		 * @param $percents 21
		 * @param $amount 21
		 * @param $attach_percent
		 * @return float
		 */
		public static function attachPercentFor($percents, $amount, $attach_percent){
			return $amount + (($amount / $percents) * $attach_percent);
		}

	}
}

