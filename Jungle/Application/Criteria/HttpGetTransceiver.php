<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 20:40
 */
namespace Jungle\Application\Criteria {

	use Jungle\Http\Request;

	/**
	 * Class HttpGetTransceiver
	 * @package Jungle\Application\Criteria
	 */
	class HttpGetTransceiver implements TransceiverInterface{

		/**
		 * @var Request
		 */
		public $request;

		/**
		 * @var
		 */
		public $receive;

		/**
		 * HttpGetTransceiver constructor.
		 * @param Request $request
		 * @param $receive
		 */
		public function __construct(Request $request,callable $receive = null){
			$this->request = $request;
			$this->receive = $receive;
		}

		/**
		 * @param $scopeKey
		 * @param Criteria $criteria
		 */
		public function receiveTo($scopeKey, Criteria $criteria){
			$criteria->limit = $criteria->limit!==null?$criteria->limit:10;
			$offset = $this->request->getQuery($this->prepareKey($scopeKey,'offset'));
			$limit = $this->request->getQuery($this->prepareKey($scopeKey,'limit'));
			if($offset!==null){
				$criteria->offset = intval($offset);
			}
			if($limit){
				$criteria->limit = intval($limit);
			}

			$this->receive && call_user_func($this->receive, $scopeKey, $criteria, $this->request);
		}

		/**
		 * @param $scopeKey
		 * @param $key
		 * @return string
		 */
		protected function prepareKey($scopeKey, $key){
			return $scopeKey?$scopeKey.'_'.$key:$key;
		}

		/**
		 * @param $scopeKey
		 * @param $index
		 * @param Scroller $scroller
		 * @return string
		 */
		public function linkPage($scopeKey, $index, Scroller $scroller){
			return '?' . http_build_query(array_replace($this->request->getQuery(), [
				$this->prepareKey($scopeKey,'offset') => $scroller->limit * $index,
				$this->prepareKey($scopeKey,'limit') => $scroller->limit
			]));
		}
	}
}

