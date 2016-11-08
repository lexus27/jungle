<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.10.2016
 * Time: 18:00
 */
namespace Jungle\Util\Communication\Net {

	/**
	 * Class Encryption
	 * @package Jungle\Util\Communication\Net
	 */
	class Encryption{

		/** @var int  */
		protected $method = 0;

		/**
		 * Encryption constructor.
		 */
		public function __construct(){
			if (defined('STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT')) {
				$this->method |= STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT;
			}
			if (defined('STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT')) {
				$this->method |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
			}
			if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
				$this->method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
			}
		}
		
		
		/**
		 * @param resource $stream_resource
		 * @return resource
		 */
		public function enable($stream_resource){
			set_error_handler([$this,'handleErrorForEnable']);
			stream_socket_enable_crypto($stream_resource, true, $this->method);
			restore_error_handler();
			return $stream_resource;
		}

		/**
		 * @param resource $stream_resource
		 * @return resource
		 */
		public function disable($stream_resource){
			set_error_handler([$this,'handleErrorForDisable']);
			stream_socket_enable_crypto($stream_resource, false, $this->method);
			restore_error_handler();
			return $stream_resource;
		}

		/**
		 * @throws \Exception
		 */
		public function handleErrorForEnable(){
			throw new \Exception('Error encryption enable');
		}

		/**
		 * @throws \Exception
		 */
		public function handleErrorForDisable(){
			throw new \Exception('Error encryption disable');
		}

		/**
		 * @return Encryption
		 */
		public static function once(){
			static $instance;
			if(!$instance){
				$instance = new self();
			}
			return $instance;
		}

	}
}

