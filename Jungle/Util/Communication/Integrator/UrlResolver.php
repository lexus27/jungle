<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 15:17
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Class UrlResolver
	 * @package Jungle\Util\Communication\Integrator
	 */
	class UrlResolver{

		protected $config = [];

		/**
		 * UrlResolver constructor.
		 * @param array|null $config
		 */
		public function __construct(array $config = null){
			$this->config = array_replace([
				'option' => 'uri',
			],$config);
		}

		/**
		 * @param Method $method
		 * @return string
		 */
		public function resolve(Method $method){
			$a = [];
			foreach($method->getNestedSegments() as $segment){
				$a[] = $segment->getOption($this->config['option']);
			}
			$a = array_filter($a);
			return implode('',$a);
		}

	}
}

