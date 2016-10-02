<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 12:32
 */
namespace Jungle\Util\Communication\Integrator {

	use Jungle\Util\Communication\Http\Request;
	use Jungle\Util\Communication\Http\Response;
	use Jungle\Util\Communication\Http\Server;

	/**
	 * Class Method
	 * @package Jungle\Util\Communication\Integrator
	 */
	class Method implements MethodInterface, ConfigurableInterface{

		/** @var  Section */
		protected $parent;

		/** @var  array */
		protected $options = [];

		/** @var  RequestManager */
		protected $request_manager;

		/** @var  UrlResolver */
		protected $url_resolver;


		/** @var  string */
		protected $method;

		/** @var  string */
		protected $scheme;

		/** @var  string */
		protected $host;

		/** @var  string */
		protected $uri;

		protected $dataType = 'application/json';


		protected $required = [];

		protected $headers  = [];

		protected $cookies  = [];

		protected $query    = [];

		protected $post     = [];

		protected $json     = [];

		protected $files;

		/**
		 * Method constructor.
		 */
		public function __construct(){
			$options = [
				'method'    => 'POST',
				'host'      => null,
				'uri'       => null,
				'type'      => 'application/json',

				'required_params' => [
					'q',
				],

				'required' => [
					'query.*',
					'post.token'
				],

				'cookies' => [
					'JUNGLE_SESSID' => '{options.auth.token}'
				],

				'query' => [
					'q' => '[params]'
				],
				'post' => [
					'token' => '{options.auth.token}'
				],
				'json' => [
					'repository' => '[params]'
				],
				'files' => '{params.files}'

			];
		}


		/**
		 * @param Section $parent
		 * @return $this
		 */
		public function setParent(Section $parent){
			$this->parent = $parent;
			return $this;
		}

		/**
		 * @return Section
		 */
		public function getParent(){
			return $this->parent;
		}


		/**
		 *
		 */
		public function getOptions(){
			if($this->parent){

			}
		}

		/**
		 * @param RequestManager $manager
		 */
		public function setRequestManager(RequestManager $manager){
			$this->request_manager = $manager;
		}

		/**
		 * @return RequestManager
		 */
		public function getRequestManager(){
			return $this->request_manager;
		}

		/**
		 * @param Request $request
		 * @return void
		 */
		public function prepareRequest(Request $request){
			$server = new Server();
			$server->setHost($this->host);

			$request->setServer($server);
			$request->setUri($this->uri);
		}

		/**
		 * @param $data
		 * @return void
		 */
		public function __invoke($data){
			$request = $this->request_manager->newRequest();
			$this->prepareRequest($request);
			$this->request_manager->sendRequest($request,[$this,'handleResponse']);
		}

		/**
		 * @param Request $request
		 * @param Response $response
		 * @param $code
		 * @param $message
		 * @return mixed
		 */
		public function handleResponse(Request $request, Response $response, $code, $message){

		}

		/**
		 *
		 */
		public function getUrlResolver(){
			return $this->url_resolver;
		}


		/**
		 * @return ConfigurableInterface[]
		 */
		public function getNestedSegments(){
			$segments = $this->parent->getNestedSegments();
			$segments[] = $this;
			return $segments;
		}

		public function getOption($key){
			// TODO: Implement getOption() method.
		}
	}
}

