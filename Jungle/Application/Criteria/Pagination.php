<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.11.2016
 * Time: 14:03
 */
namespace Jungle\Application\Criteria {

	/**
	 * Class Pagination
	 * @package Jungle\Application\Criteria
	 */
	class Pagination{

		protected $limit;

		protected $page_index;

		protected $page_count;

		protected $total;

		public function getPageCount(){

		}

		public function getPageIndex(){

		}

		public function provideIndex(){
			return 0;
		}

		public function provideTotal(){
			return 65;
		}

		public function getParams(){

			$limit = $this->limit; // 10 ; 10 ; 10
			$total = $this->provideTotal(); // 65 ; 60 ; 61

			$pages_multiplier = $total / $limit; // 6,5 ; 6 ; 6,1
			$pages_count = round($pages_multiplier, 0, PHP_ROUND_HALF_UP); // 7 ; 6 ; 7
			///** if last page  (entries < limit) = (tail > 0) */
			//$tail = $limit * ($pages_count - $pages_multiplier); // 5 ; 0 ; 1

			$current_page_index = $this->provideIndex(); // 6 ; 5 ; 6

			if($current_page_index < 0){
				throw new \Exception('Wrong page_index, must be greater than 0');
			}
			if($pages_count <= $current_page_index){
				throw new \Exception('Wrong page_index, must be less than (pages_count)'.$pages_count.', passed "'.$current_page_index.'"');
			}

			$offset = $current_page_index * $limit; // 60 ; 50 ; 60

			return [
				'limit' => $limit,
				'offset' => $offset,
			];
		}

	}
}

