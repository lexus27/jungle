<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 21:37
 */
namespace Jungle\Application\Criteria {

	use Jungle\Util\Communication\HttpFoundation\RequestInterface;

	/**
	 * Class HttpStructuralTransceiver
	 * @package Jungle\Application\Criteria
	 */
	class HttpStructuralTransceiver implements TransceiverInterface{
		/**
		 * @var RequestInterface
		 */
		private $request;


		/**
		 * HttpStructuralTransceiver constructor.
		 * @param RequestInterface $request
		 */
		public function __construct(RequestInterface $request){
			$this->request = $request;
		}

		/**
		 * @param $scopeKey
		 * @param Criteria $criteria
		 */
		public function receiveTo($scopeKey, Criteria $criteria){
			$condition = $this->request->getParam('condition', null);
			$order = $this->request->getParam('order', null);
			$offset = $this->request->getParam('offset', null);
			$limit = $this->request->getParam('limit', null);

			isset($condition) && ($criteria->condition = $condition);
			isset($order) && ($criteria->order = $order);
			isset($offset) && ($criteria->offset = $offset);
			isset($limit) && ($criteria->limit = $limit);
			if(!isset($criteria->limit)){
				$criteria->limit = 10;
			}
		}

		/**
		 * @param $scopeKey
		 * @param $index
		 * @param Scroller $scroller
		 * @return string
		 */
		public function linkPage($scopeKey, $index, Scroller $scroller){
			return '?' . http_build_query(array_replace($this->request->getParam(),[
				'offset' => $scroller->offset,
				'limit' => $scroller->limit
			]));
		}
	}
}

