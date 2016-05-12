<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.05.2016
 * Time: 19:55
 */
namespace Jungle\Application {

	/**
	 * Class Response
	 * @package Jungle\Application
	 */
	abstract class Response implements ResponseInterface{

		/** @var  string */
		protected $formatterType;

		/** @var  string */
		protected $content;

		/** @var  RequestInterface */
		protected $request;

		/**
		 * @return RequestInterface
		 */
		public function getRequest(){
			return $this->request;
		}

		/**
		 * @param $formatterType
		 * @return $this
		 */
		public function setFormatterType($formatterType){
			$this->formatterType = $formatterType;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getFormatterType(){
			return $this->formatterType;
		}

		/**
		 * @return mixed
		 */
		public function getContent(){
			return $this->content;
		}

		/**
		 * Send content and depends values to client
		 * @return bool
		 * @throws \Exception
		 */
		public function send(){
			// TODO: Implement send() method.
		}


	}
}

