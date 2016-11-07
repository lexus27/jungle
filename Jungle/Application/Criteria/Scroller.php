<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 04.11.2016
 * Time: 23:30
 */
namespace Jungle\Application\Criteria {

	/**
	 * Class Scroller
	 * @package Jungle\Application\Criteria
	 *
	 * Данная реализация представляет собой набор методов способных помоч при генерировании Разметки в Отображениях
	 * Как видно из семантики кода, мы не используем ни единого намека на жесткую верстку здесь.
	 * Также реализация не требует зависимости от ORM, источника данных и коллекций
	 * В данном классе происходит лишь генерация ссылки на страницу, далее уже можно оформить эту ссылку как угодно
	 *
	 */
	class Scroller implements ScrollerInterface, \Countable{

		public $pages;
		public $current;

		public $total_count;

		public $offset;
		public $limit;

		public $link_creator;


		/**
		 * Scroller constructor.
		 * @param $total_count
		 * @param $limit
		 * @param $offset
		 * @param callable $link_creator
		 */
		public function __construct($total_count, $limit, $offset, callable $link_creator){
			$this->limit        = $limit;
			$this->offset       = $offset;
			$this->total_count  = $total_count;
			$this->link_creator = $link_creator;
			$this->calculates();
		}


		/**
		 * @param callable $link_creator
		 * @return $this
		 */
		public function setLinkCreator(callable $link_creator){
			$this->link_creator = $link_creator;
			return $this;
		}


		/**
		 * @return float
		 */
		public function end(){
			return $this->createLink($this->pages - 1);
		}

		/**
		 * @return string
		 */
		public function endIndex(){
			return $this->pages - 1;
		}


		/**
		 * @return mixed
		 */
		public function count(){
			return $this->pages;
		}


		/**
		 * @return array
		 */
		public function links(){
			$a = [];
			$pages = $this->pages;
			for($i = 0; $i < $pages; $i++){
				if(($link = $this->createLink($i)) !== null){
					$a[$i] = $this->createLink($i);
				}
			}
			return $a;
		}

		/**
		 * @param int $skip
		 * @return string|null
		 */
		public function prev($skip = 0){
			$skip++;
			$prevIndex = $this->current - $skip;
			return $prevIndex < 0?null:$this->createLink($prevIndex);
		}


		/**
		 * @param int $skip
		 * @return string|null
		 */
		public function next($skip = 0){
			$skip++;
			$nextIndex = $this->current + $skip;
			return $nextIndex < $this->pages?$this->createLink($nextIndex):null;
		}

		/**
		 * @param int $skip
		 * @return null
		 */
		public function prevIndex($skip = 0){
			$skip++;
			$prevIndex = $this->current - $skip;
			return $prevIndex < 0?null:$prevIndex;
		}

		/**
		 * @param int $skip
		 * @return null
		 */
		public function nextIndex($skip = 0){
			$skip++;
			$nextIndex = $this->current + $skip;
			return $nextIndex < $this->pages?$nextIndex:null;
		}



		/**
		 * @param $start
		 * @param $amount
		 * @return array
		 */
		public function sliceLinks($start, $amount){
			$a = [];
			for($i = 0, $pageIndex = $start; $i < $amount;$i++,$pageIndex++){
				$link = $this->link($pageIndex);
				if($link!==false){
					$a[$pageIndex] = $link;
				}
			}
			return $a;
		}


		/**
		 * @return mixed
		 */
		public function countBefore(){
			return $this->current;
		}


		/**
		 * @return mixed
		 */
		public function countAfter(){
			return $this->pages - ($this->current+1);
		}


		/**
		 * @param int $count
		 * @return array
		 */
		public function linksBefore($count = 1){
			$start = $this->current - $count;
			if($start < 0){
				$amount = $count - abs($start);
				return $amount?$this->sliceLinks(0,$amount):[];
			}else{
				return $this->sliceLinks($start,$count);
			}
		}



		/**
		 * @param int $count
		 * @return array
		 */
		public function linksAfter($count = 1){
			$after = $this->pages - ($this->current+1);
			$amount = $count > $after?$after:$count;
			return $amount?$this->sliceLinks($this->current + 1,$amount):[];
		}

		/**
		 * @param $index
		 * @return mixed
		 */
		public function pageOffset($index){
			return $this->limit * $index;
		}


		/**
		 * @param $pageIndex
		 * @return bool
		 */
		public function link($pageIndex){
			if($pageIndex < $this->pages && $pageIndex >= 0){
				return $this->createLink($pageIndex);
			}else{
				return false;
			}
		}

		/**
		 * @return mixed
		 */
		public function current(){
			return $this->current;
		}


		/**
		 * @param $index
		 * @return string
		 */
		protected function createLink($index){
			return call_user_func($this->link_creator,$index, $this);
		}


		protected function calculates(){
			$this->pages = intval(ceil($this->total_count / $this->limit));
			$this->current = intval(floor($this->offset / $this->limit));
			if($this->current >= $this->pages){
				$this->current = $this->pages - 1;
			}
		}

		/**
		 *
		 */
		public function provide(){
			$this->limit        = $this->provideLimit();
			$this->offset       = $this->provideOffset();
			$this->total_count  = $this->provideTotalCount();
			$this->calculates();
		}

		protected function provideLimit(){
			return 10;
		}

		protected function provideOffset(){
			return 0;
		}

		protected function provideTotalCount(){
			return 97;
		}


	}
}

