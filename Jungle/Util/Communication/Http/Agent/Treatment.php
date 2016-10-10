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
	
	use Jungle\Util\Communication\Connection\Stream\Socket;
	use Jungle\Util\Communication\Connection\StreamInterface;
	use Jungle\Util\Communication\Http\Agent;
	use Jungle\Util\Communication\Http\Request;
	use Jungle\Util\Communication\Http\Response;

	/**
	 * Class Treatment
	 * @package Jungle\Util\Communication\Http
	 */
	class Treatment{

		/** @var Agent */
		protected $agent;

		/** @var  Request */
		protected $request;

		/** @var  StreamInterface */
		protected $stream;


		/**
		 * Treatment constructor.
		 * @param Agent $agent
		 * @param Request|null $request
		 * @param StreamInterface|null $stream
		 */
		public function __construct(Agent $agent, Request $request = null, StreamInterface $stream = null){
			$this->request = $request;
			$this->stream = $stream;
			$this->agent = $agent;
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


		/**
		 * @param StreamInterface $stream
		 * @return $this
		 */
		public function setStream(StreamInterface $stream){
			$this->stream = $stream;
			return $this;
		}
		/**
		 * @return StreamInterface
		 */
		public function getStream(){
			return $this->stream;
		}






		public function execute(){
			$request = $this->request;
			if(!$this->stream){
				$this->stream = new Socket([
					'host'          => $request->getServer()->getHost(),
					'port'          => $request->isSecure()?443:$request->getServer()->getPort(),
					'transport'     => $request->isSecure()?'ssl':null,
					'timeout'       => null,
				]);
			}

			$this->stream->connect();








		}


		/**
		 * @param Response $response
		 */
		public function onResponse(Response $response){

		}


	}
}

