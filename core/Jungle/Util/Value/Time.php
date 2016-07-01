<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 16:22
 */
namespace Jungle\Util\Value {

	class Time{

		protected function __construct(){}

		/**
		 * @var array
		 */
		protected static $seasons = [
			'Winter' => ['December', 'January', 'February'],
			'Spring' => ['March', 'April' , 'May',],
			'Summer' => ['June' , 'July', 'August'],
			'Autumn' => ['September', 'October', 'November']
		];

		/**
		 * @var array
		 */
		protected static $year_half = [
			'First'     => [1,2,3,4,5,6],
			'Second'    => [7,8,9,10,11,12],
		];

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function seasonIsWinter($time = null){
			return in_array(date('F',$time),self::$seasons['Winter'],true);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function seasonIsSpring($time = null){
			return in_array(date('F',$time),self::$seasons['Spring'],true);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function seasonIsSummer($time = null){
			return in_array(date('F',$time),self::$seasons['Summer'],true);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function seasonIsAutumn($time = null){
			return in_array(date('F',$time),self::$seasons['Autumn'],true);
		}


		/**
		 * @param null $time
		 * @return bool
		 */
		public static function isFirstYearHalf($time = null){
			return in_array(date('n',$time),self::$year_half['First']);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function isSecondYearHalf($time = null){
			return in_array(date('n',$time),self::$year_half['Second']);
		}

		/**
		 * @param null $time
		 * @return int|null|string
		 */
		public static function getYearHalf($time = null){
			$mNumber = date('n',$time);
			foreach(self::$year_half as  $half => $mNumbers){
				if(Massive::stringExists($mNumbers,$mNumber,true)){
					return $half;
				}
			}
			return null;
		}


		/**
		 * @param null $time
		 * @return string
		 */
		public static function getSeason($time = null){
			$month = date('F',$time);
			foreach(self::$seasons as  $season => $months){
				if(Massive::stringExists($months,$month,true)){
					return $season;
				}
			}
			return null;
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function getMonth($time = null){
			return date('F',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function getWeekday($time = null){
			return date('l',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function getHour($time = null){
			return date('H',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function getMinutes($time = null){
			return date('i',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function getSec($time = null){
			return date('s',$time);
		}

		const MERIDIEM_AM = 'am';
		const MERIDIEM_PM = 'pm';

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function getMeridiem($time = null){
			return date('a',$time);
		}


		/**
		 * Достигнутый период.
		 * Достигнуло ли $time, Срока назначенного со $startCheckpoint на $delay период
		 * @param $startCheckpoint
		 * @param $delay
		 * @param null $time
		 * @return bool
		 *      $reason = 'test reason';
		 *      $delay = 3600; // 1 hour
		 *      $account->lock($delay, $reason);
		 *      Account{
		 *
		 *           protected $locked, $locked_time, $locked_delay, $locked_reason,
		 *
		 *           function lock($delay, $reason){}
		 *
		 *           function isLocked($auto_amnesty){
		 *               if($auto_amnesty && $this->locked && Time::isReached($this->locked_time, $this->locked_delay)){
		 *                   $this->amnesty();
		 *               }
		 *               return $this->locked;
		 *           }
		 *      }
		 */
		public static function isReached($startCheckpoint, $delay, $time = null){
			if($time===null) $time = microtime(true);
			return ($startCheckpoint + $delay) >= $time;
		}

		/**
		 * Просроченный период
		 * Прошло ли $time , время назначенное со $startCheckpoint на $delay период
		 * @param $startCheckpoint
		 * @param $delay
		 * @param null $time
		 * @return bool
		 */
		public static function isOverdue($startCheckpoint, $delay, $time = null){
			if($time===null) $time = microtime(true);
			return ($startCheckpoint + $delay) > $time;
		}

		/**
		 * Время, которое прошло спустя $checkpoint
		 * @param $checkpoint
		 * @param null $time
		 * @return mixed|null
		 */
		public static function getElapsedOf($checkpoint , $time = null){
			if($time===null) $time = microtime(true);
			return $time - $checkpoint;
		}

		/**
		 * Время, которое осталось до $checkpoint
		 * @param $checkpoint
		 * @param null $time
		 * @return mixed|null
		 */
		public static function getRemainsOf($checkpoint, $time = null){
			if($time===null) $time = microtime(true);
			return $checkpoint - $time;
		}




	}
}

