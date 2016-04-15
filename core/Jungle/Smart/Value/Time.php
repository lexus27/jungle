<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 16.02.2016
 * Time: 23:58
 */
namespace Jungle\Smart\Value {

	/**
	 * Class Time
	 * @package Jungle\Smart\Value
	 * @TODO implements . Значение времени по стандартам
	 *
	 * Единственная трудность заключается в том что время как таковое имеет множество аспектов своего применения.
	 * UTS - Unix Time Stamp
	 * UTS и Время года или Месяц или День недели. совершенно не имеют ничего общего
	 *
	 * Расмотрим несколько подтипов базового времени:
	 * Дата     - UTS , Format
	 * Половина года    вычисляется из даты.
	 * Сезон            вычисляется из даты.
	 * День недели      вычисляется из даты.
	 * Месяц.           вычисляется из даты.
	 * Час.             вычисляется из даты.
	 * Минуты.          вычисляется из даты.
	 * Секунды.         вычисляется из даты.
	 * Миллисекунды.    вычисляется из даты.
	 *
	 *
	 * Так-же имеются второстипенные типы.
	 *      Интервалы, Промежутки, Периоды, то есть какоето выделенное количество времени
	 *
	 * TimeAmount - выделенное количество времени
	 *
	 * Милисекунды.
	 * Секунды.
	 * Минуты.
	 * Часы.
	 * Полудни.
	 * Сутки.
	 * Недели.
	 * Месяца.
	 * Сезоны.
	 * Полугодия.
	 * Года.
	 * Десятилетия.
	 * Века.
	 *
	 */
	class Time extends Number{

		/**
		 * @return null
		 */
		public static function getDefaultRawValue(){
			return microtime(true);
		}

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
		public static function timeSeasonIsWinter($time = null){
			return in_array(date('F',$time),self::$seasons['Winter'],true);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function timeSeasonIsSpring($time = null){
			return in_array(date('F',$time),self::$seasons['Spring'],true);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function timeSeasonIsSummer($time = null){
			return in_array(date('F',$time),self::$seasons['Summer'],true);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function timeSeasonIsAutumn($time = null){
			return in_array(date('F',$time),self::$seasons['Autumn'],true);
		}


		/**
		 * @param null $time
		 * @return bool
		 */
		public static function timeIsFirstYearHalf($time = null){
			return in_array(date('n',$time),self::$year_half['First']);
		}

		/**
		 * @param null $time
		 * @return bool
		 */
		public static function timeIsSecondYearHalf($time = null){
			return in_array(date('n',$time),self::$year_half['Second']);
		}

		/**
		 * @param null $time
		 * @return int|null|string
		 */
		public static function timeGetYearHalf($time = null){
			$mNumber = date('n',$time);
			foreach(self::$year_half as  $half => $mNumbers){
				if(Arr::arrStringExists($mNumbers,$mNumber,true)){
					return $half;
				}
			}
			return null;
		}


		/**
		 * @param null $time
		 * @return string
		 */
		public static function timeGetSeason($time = null){
			$month = date('F',$time);
			foreach(self::$seasons as  $season => $months){
				if(Arr::arrStringExists($months,$month,true)){
					return $season;
				}
			}
			return null;
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function timeGetMonth($time = null){
			return date('F',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function timeGetWeekday($time = null){
			return date('l',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function timeGetHour($time = null){
			return date('H',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function timeGetMinutes($time = null){
			return date('i',$time);
		}

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function timeGetSec($time = null){
			return date('s',$time);
		}

		const MERIDIEM_AM = 'am';
		const MERIDIEM_PM = 'pm';

		/**
		 * @param null $time
		 * @return bool|string
		 */
		public static function timeGetMeridiem($time = null){
			return date('a',$time);
		}


		/**
		 * Достигнутый срок
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
		 *               if($auto_amnesty && $this->locked && Time::timeIsReached($this->locked_time, $this->locked_delay)){
		 *                   $this->amnesty();
		 *               }
		 *               return $this->locked;
		 *           }
		 *      }
		 */
		public static function timeIsReached($startCheckpoint, $delay, $time = null){
			if($time===null) $time = microtime(true);
			return ($startCheckpoint + $delay) >= $time;
		}

		/**
		 * Просроченый срок
		 * @param $startCheckpoint
		 * @param $delay
		 * @param null $time
		 * @return bool
		 */
		public static function timeIsOverdue($startCheckpoint, $delay, $time = null){
			if($time===null) $time = microtime(true);
			return ($startCheckpoint + $delay) > $time;
		}

		/**
		 * @param $checkpoint
		 * @param null $time
		 * @return mixed|null
		 */
		public static function timeGetElapsedOf($checkpoint , $time = null){
			if($time===null) $time = microtime(true);
			return $time - $checkpoint;
		}

		/**
		 * @param $checkpoint
		 * @param null $time
		 * @return mixed|null
		 */
		public static function timeGetRemainsOf($checkpoint, $time = null){
			if($time===null) $time = microtime(true);
			return $checkpoint - $time;
		}



	}
}

