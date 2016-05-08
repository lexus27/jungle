<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:17
 */
namespace Jungle\Application\Dispatcher {

	use Jungle\Application\Dispatcher\Context;
	use Jungle\Application\Dispatcher\Reference\ModularReferenceInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\RegExp\Template;

	/**
	 * Interface RouterInterface
	 * @package Jungle\Application
	 */
	interface RouterInterface{

		/**
		 * @param RequestInterface $request
		 * @return ModularReferenceInterface|false
		 */
		public function match(RequestInterface $request);

		/**
		 * @param $reference
		 * @param array $params
		 * @return mixed
		 */
		public function generateLink(array $params = null, $reference = null);

		/**
		 * @param $route_alias
		 * @param array $params
		 * @param null $reference
		 * @return mixed
		 */
		public function generateLinkBy($route_alias, array $params = null,$reference = null);

		/**
		 * @return Template
		 */
		public function getTemplateManager();

		/**
		 * @return string
		 */
		public function getSpecialParamPrefix();

		/**
		 * @param string $path
		 * @return string
		 */
		public function normalizePath($path);
	}
}

