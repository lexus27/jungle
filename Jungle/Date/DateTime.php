<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.12.2016
 * Time: 2:10
 */
namespace Jungle\Date {

	/**
	 * Class DateTime
	 * @package Jungle\Date
	 */
	class DateTime extends \DateTime{

		const SEASON_SUMMER = 'Summer';
		const SEASON_AUTUMN = 'Autumn';
		const SEASON_SPRING = 'Spring';
		const SEASON_WINTER = 'Winter';

		const WEAK_MON = 'Monday';
		const WEAK_TUE = 'Tuesday';
		const WEAK_WED = 'Wednesday';
		const WEAK_THU = 'Thursday';
		const WEAK_FRI = 'Friday';
		const WEAK_SAT = 'Saturday';
		const WEAK_SUN = 'Sunday';


		const MONTH_JAN = 'January';
		const MONTH_FEB = 'February';
		const MONTH_MAR = 'March';
		const MONTH_APR = 'April';
		const MONTH_MAY = 'May';
		const MONTH_JUN = 'June';
		const MONTH_JUL = 'July';
		const MONTH_AUG = 'August';
		const MONTH_SEP = 'September';
		const MONTH_OCT = 'October';
		const MONTH_NOV = 'November';
		const MONTH_DEC = 'December';


		public function isFirstHalfYear(){
			return date('n',$this->getTimestamp()) <= 6;
		}

		public function isSecondHalfYear(){
			return date('n',$this->getTimestamp()) > 6;
		}


		public function isLeapYear(){
			return date('L',$this->getTimestamp());
		}

		public function getYearDayIndex(){
			return date('z',$this->getTimestamp());
		}

		/**
		 * @return null|string
		 */
		public function getSeason(){
			$month = date('n',$this->getTimestamp());
			if(in_array($month,[12,1,2])){
				return self::SEASON_WINTER;
			}
			if(in_array($month,[3,4,5])){
				return self::SEASON_SPRING;
			}
			if(in_array($month,[6,7,8])){
				return self::SEASON_SUMMER;
			}
			if(in_array($month,[9,10,11])){
				return self::SEASON_AUTUMN;
			}
			return null;
		}

		public function isWinter(){
			return in_array(date('n',$this->getTimestamp()),[12,1,2]);
		}

		public function isSpring(){
			return in_array(date('n',$this->getTimestamp()),[3,4,5]);
		}

		public function isSummer(){
			return in_array(date('n',$this->getTimestamp()),[6,7,8]);
		}

		public function isAutumn(){
			return in_array(date('n',$this->getTimestamp()),[9,10,11]);
		}





		public function isWeekEnd(){
			return in_array(date('N',$this->getTimestamp()),[6,7]);
		}

		public function getWeekDay(){
			$number = intval(date('N',$this->getTimestamp()));
			return $number;
		}

		public function getYear(){
			return date('Y',$this->getTimestamp());
		}

		public function getDay(){
			return date('d',$this->getTimestamp());
		}

		public function getMonthDaysCount(){
			return date('t',$this->getTimestamp());
		}


		public function getMonth(){
			return date('n',$this->getTimestamp());
		}


		public function getHours(){
			return date('H',$this->getTimestamp());
		}

		public function getMinutes(){
			return date('i',$this->getTimestamp());
		}

		public function getSeconds(){
			return date('s',$this->getTimestamp());
		}

		public function getMicroSeconds(){
			return date('u',$this->getTimestamp());
		}



		/**
		 * @return array
		 */
		public static function getWeekDays(){
			return [
				self::WEAK_MON, self::WEAK_TUE, self::WEAK_WED,
				self::WEAK_THU, self::WEAK_FRI, self::WEAK_SAT,
				self::WEAK_SUN,
			];
		}


		/**
		 * @return array
		 */
		public static function getMonths(){
			return [
				self::MONTH_JAN, self::MONTH_FEB, self::MONTH_MAR,
				self::MONTH_APR, self::MONTH_MAY, self::MONTH_JAN,
				self::MONTH_JUL, self::MONTH_AUG, self::MONTH_SEP,
				self::MONTH_OCT, self::MONTH_NOV, self::MONTH_DEC,
			];
		}


		/**
		 * @return array
		 */
		public static function getSeasons(){
			return [
				self::SEASON_WINTER, self::SEASON_SPRING,
				self::SEASON_SUMMER, self::SEASON_AUTUMN
			];
		}

	}
}

