<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.10.2016
 * Time: 0:09
 */
namespace Jungle\Util\Communication\Http\Agent {
	
	use Jungle\Util\Communication\Http\Response;

	/**
	 * Class Navigation
	 * @package Jungle\Util\Communication\Http\Agent
	 */
	class Navigation{

		/** @var  Response[] */
		protected $nav_pages = [];

		/** @var  int|null */
		protected $nav_current_index;

		/** @var  int  */
		protected $nav_pages_count = 0;

		/**
		 * @param Response $response
		 */
		public function setNavNext(Response $response){
			if(!empty($this->nav_pages) && $this->nav_current_index < $this->nav_pages_count){
				array_splice($this->nav_pages, $this->nav_current_index + 1, $this->nav_pages_count, [$response]);
				$this->nav_pages_count = count($this->nav_pages);
			}else{
				$this->nav_pages_count++;
			}
			$this->nav_pages[] = $response;
			if($this->nav_current_index === null){
				$this->nav_current_index = 0;
			}else{
				$this->nav_current_index++;
			}
		}

		/**
		 * @return Response|null
		 */
		public function currentNavPage(){
			return $this->nav_current_index !== null?$this->nav_pages[$this->nav_current_index]:null;
		}

		/**
		 * @return bool
		 */
		public function hasNavBack(){
			return $this->nav_current_index > 0;
		}
		
		/**
		 * @return $this
		 */
		public function navBack(){
			if($this->nav_current_index > 0){
				$this->nav_current_index--;
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasNavNext(){
			return $this->nav_current_index + 1 < $this->nav_pages_count;
		}

		/**
		 * @return $this
		 */
		public function navNext(){
			if($this->nav_current_index + 1 < $this->nav_pages_count){
				$this->nav_current_index++;
			}
			return $this;
		}


		/**
		 * @return $this
		 */
		public function navClear(){
			$this->nav_pages = [];
			$this->nav_pages_count = 0;
			$this->nav_current_index = null;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function navClearOther(){
			$current = $this->currentNavPage();
			$this->nav_pages = [$current];
			$this->nav_pages_count = 1;
			$this->nav_current_index = 0;
			return $this;
		}



		/**
		 * @return int
		 */
		public function getNavPagesCount(){
			return $this->nav_pages_count;
		}

		/**
		 * @return float
		 */
		public function getNavProgress(){
			return round((($this->nav_pages_count / 100) * ($this->nav_current_index + 1)) * 100,2);
		}


	}
}

