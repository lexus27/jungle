<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 29.01.2016
 * Time: 20:15
 */
namespace Jungle {

	/**
	 * Class Date
	 * @package Jungle
	 */
	class Date{

		protected $bc = false;

		protected $year = null;

		protected $moth = null;

		protected $day = null;

		protected $hours = null;

		protected $minutes = null;

		protected $seconds = null;

		protected $milliseconds = null;

		/**
		 * @param $timestamp
		 */
		public function fromUnix($timestamp){
			list(
				$this->year,
				$this->moth,
				$this->day,
				$this->hours,
				$this->minutes,
				$this->seconds,
				$this->milliseconds
			) = array_map('intval',explode(' ',date('Y m d G i s u',$timestamp)));
		}


		public function render(){
			return
				$this->year .
				'-' . $this->moth .
				'-' . $this->day .
				'T' . $this->hours .
				':' . $this->minutes .
				':' . $this->seconds .
				'.' . intval($this->milliseconds);
		}

		/**
		 * @param $date
		 */
		public function parse($date){
			if(preg_match('@(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+).(\d+)@',$date,$m)){
				array_shift($m);
				list(
					$this->year,
					$this->moth,
					$this->day,
					$this->hours,
					$this->minutes,
					$this->seconds,
					$this->milliseconds
					) = $m;
			}
		}


	}
}

