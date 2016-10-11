<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.10.2016
 * Time: 14:41
 */
namespace Jungle\Util\Communication\Http\Agent {

	use Jungle\Util\Communication\Http\Agent;
	use Jungle\Util\Communication\Http\Request;
	use Jungle\Util\Communication\Http\Response;

	/**
	 * Это единица обращения.
	 * В обращения складываются цепочки запросов-ответов из Редиректов.
	 * Обращение важно с точки зрения "истории" и навигации "вперед" / "назад"
	 *
	 * Поток не обязательно закрывать между запросами,
	 * если поток подключен к одному и тому же адресу с портом, то закрытие и открытие между запросами, увеличит время выполнения
	 *
	 * Treatment Призван группировать серию запрос-ответ
	 *
	 * Где между запросом и конечным ответом есть дополнительные цепочки.
	 *
	 * Запрос - Ответ(Location)
	 * Запрос - Ответ(Location)
	 * Запрос - Ответ (Result)
	 *
	 * Между каждой серией Браузер должен сохранять куки и кешировать ответы, которые требуется кешировать
	 *
	 * Class Treatment
	 * @package Jungle\Util\Communication\Http
	 */
	class Treatment{

		/** @var Agent */
		protected $agent;

		/** @var  Request */
		protected $request;

		/**
		 * Treatment constructor.
		 * @param Agent $agent
		 * @param Request|null $request
		 */
		public function __construct(Agent $agent, Request $request = null){
			$this->agent = $agent;
			$this->request = $request;
		}

		/**
		 * @param Request $request
		 * @return $this
		 */
		public function setRequest(Request $request){
			$this->request = $request;
			return $this;
		}
		/**
		 * @return Request
		 */
		public function getRequest(){
			return $this->request;
		}



		public function execute(){
			$request = $this->request;





		}


		/**
		 * @param Response $response
		 */
		public function onResponse(Response $response){

		}

		/**
		 * @param Request $request
		 */
		protected function _series(Request $request){
			$this->agent->normalizeRequest($request);
			$streamer = $this->agent->getNetworkManager();
			$this->agent->execute();
			$stream = $streamer->prepareStream($request->getServer()->getIp(),$request->isSecure());
			$stream->connect();
			$response = $this->agent->process($request, $stream);
			$this->agent->onResponse($response);
		}


		protected function successTreatment(){

		}

		protected function failureTreatment(){

		}

		protected function continueTreatment(){

		}


	}
}

