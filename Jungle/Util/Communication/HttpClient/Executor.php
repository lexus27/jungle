<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.10.2016
 * Time: 23:24
 */
namespace Jungle\Util\Communication\HttpClient {

	use Jungle\Util\Communication\Hypertext\Document\ReadProcessor;
	use Jungle\Util\Communication\Hypertext\Document\WriteProcessor;
	use Jungle\Util\Communication\Hypertext\DocumentInterface;
	use Jungle\Util\Communication\Net\ConnectorInterface;
	use Jungle\Util\Communication\Net\SecureConnector;
	use Jungle\Util\Communication\Net\Stream;
	use Jungle\Util\Communication\Net\TcpConnector;
	
	/**
	 * Class Executor
	 * @package Jungle\Util\Communication\HttpClient
	 */
	abstract class Executor{

		/** @var  Network */
		protected $network;

		/** @var  WriteProcessor */
		protected $_write_processor;

		/** @var  ReadProcessor */
		protected $_read_processor;

		/** @var   */
		protected $_secure_connector;

		/**
		 * @param Network $network
		 * @return $this
		 */
		public function setNetwork(Network $network){
			$this->network = $network;
			return $this;
		}

		/**
		 * @return Network
		 */
		public function getNetwork(){
			if(!$this->network){
				$this->network = new Network();
			}
			return $this->network;
		}

		/**
		 * @param Request $request
		 * @return Response
		 */
		public function execute(Request $request){
			$stream = $this->getStreamToRequest($request);
			/** Request sending... */
			$request->setLoading(true);
			$this->getWriterFor($request)->process($stream);
			/** Response reading... */
			$response = $request->getResponse();
			if(!$response->isReused()){
				$response->setLoading(true);
				$response = $this->getReaderFor($response)->process($stream);
			}
			return $response;
		}

		/**
		 * @param Request $request
		 * @return bool
		 * @throws \Exception
		 */
		public function push(Request $request){
			$stream = $this->getStreamToRequest($request);
			$request->setLoading(true);
			$this->getWriterFor($request)->process($stream);
			if($request->getOption('cache_manager::from_cache')){
				return $request->getResponse();
			}
			return true;
		}

		/**
		 * @param Request $request
		 * @return Response|\Jungle\Util\Communication\HttpFoundation\ResponseInterface|DocumentInterface
		 * @throws \Exception
		 */
		public function pull(Request $request){
			$stream = $request->getOption(Server::EXECUTION_STREAM);
			$response = $request->getResponse();
			if(!$response->isReused()){
				$response->setLoading(true);
				$response = $this->getReaderFor($response)->process($stream);
			}
			return $response;
		}

		/**
		 * @param DocumentInterface $document
		 * @return WriteProcessor
		 */
		protected function getWriterFor(DocumentInterface $document){
			if(!$this->_write_processor){
				$this->_write_processor = new WriteProcessor(null,true,false,'');
			}
			return $this->_write_processor->setDocument($document);
		}

		/**
		 * @param DocumentInterface $document
		 * @return ReadProcessor
		 */
		protected function getReaderFor(DocumentInterface $document){
			if(!$this->_read_processor){
				$this->_read_processor = new ReadProcessor(null,true,false,'');
			}
			return $this->_read_processor->setDocument($document);
		}

		/**
		 * @param Request $request
		 * @return Stream
		 * @throws \Exception
		 */
		protected function getStreamToRequest(Request $request){
			$network = $this->getNetwork();
			$server = $request->getServer();
			if(strcasecmp($request->getScheme(), 'https') === 0){
				$connector = $this->getSecureConnector();
			}else{
				$connector = $this->getConnector();
			}
			return $network->take($server->getHost(), $server->getPort(), $connector);
		}

		/**
		 * @param $host
		 * @param $scheme
		 * @return Server
		 */
		public function getServer($host, $scheme){
			$network = $this->getNetwork();
			$server = $network->getServer($host, $scheme);
			return $server;
		}

		/**
		 * @return ConnectorInterface
		 */
		protected function getConnector(){
			return TcpConnector::onceDefault();
		}

		/**
		 * @return SecureConnector
		 */
		protected function getSecureConnector(){
			if(!$this->_secure_connector){
				$this->_secure_connector = new SecureConnector($this->getConnector());
			}
			return $this->_secure_connector;
		}


	}
	
}

