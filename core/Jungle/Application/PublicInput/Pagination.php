<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.07.2016
 * Time: 22:20
 */
namespace Jungle\Application\PublicInput {

	/**
	 * Class Pagination
	 * @package Jungle\Application\PublicInput
	 */
	class Pagination{

		/** @var  int */
		protected $total_count;

		/** @var  int */
		protected $limit;

		/**
		 * @param  $count
		 * @return $this
		 */
		public function setTotal($count){
			$this->total_count = $count;
			return $this;
		}

		/**
		 * @param $limit
		 * @return $this
		 */
		public function setLimit($limit){
			$this->limit = $limit;
			return $this;
		}

		/**
		 * @param int $count
		 * @return array
		 */
		public function getLatestPageNums($count = 1){
			$max = $this->getPagesCount();
			$start = $max - $count;
			for($i=$start;$i<=$max;$i++){
				$a[] = $i;
			}

			if($count > $max){
				$a = [];

				return $a;
			}
		}

		/**
		 * @param int $count
		 */
		public function getStartsPageNums($count = 1){
			$max = $this->getPagesCount();
		}

		/**
		 * @param int $count
		 * @return array
		 */
		public function getNextPageNums($count = 1){
			$max = $this->getPagesCount();
			$a = [];
			for($i = $this->getCurrentPageNum()+1,$elapsed = 0; $i <= $max && $elapsed < $count; $i++,$elapsed++){
				$a[] = $i;
			}
			return $a;
		}

		/**
		 * @param $count
		 * @return int
		 */
		public function getPrevPageNums($count = 1){
			$a = [];
			for($i = $this->getCurrentPageNum()-1,$elapsed = 0; $i > 0 && $elapsed < $count; $i--,$elapsed++){
				$a[] = $i;
			}
			return $a;
		}


		/**
		 * @return int
		 */
		public function getCurrentPageNum(){
			return 1;
		}

		/**
		 * @return int
		 */
		public function getPagesCount(){
			return intval(ceil($this->total_count / $this->limit));
		}

		/**
		 * @param $num
		 */
		public function generateLink($num = 1){

		}

		public function getPool(){

			$maxPagePositions = 20;
			$chunkCount = 7;
			$nextControl = true;
			$prevControl = true;
			$targetField = true;



			$pages = $this->getPagesCount();

			$current = $this->getCurrentPageNum();

			if($current === 1){

				if($pages <= $maxPagePositions){
					$a = [];
					for($i=1;$i<=$pages;$i++){
						$a[] = $i;
					}
					return $a;
				}elseif($pages > $maxPagePositions * 10){




				}else{
					$next = $this->getNextPageNums($chunkCount);
					array_unshift($next,1);
					$dotCount = $maxPagePositions - ($chunkCount + $chunkCount);
					$dots = array_fill(0,$dotCount,'.');
					$latest = $this->getLatestPageNums($chunkCount);
					return array_merge($next , $dots, $latest);
				}

			}



			$prev = $this->getPrevPageNums(10);

		}



	}
}

