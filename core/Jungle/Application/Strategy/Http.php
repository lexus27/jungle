<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.07.2016
 * Time: 23:19
 */
namespace Jungle\Application\Strategy {
	
	use Jungle\Application\RequestInterface;
	use Jungle\Application\ResponseInterface;
	use Jungle\Application\Strategy;
	use Jungle\Application\Strategy\Http\Router as HTTP_Router;
	use Jungle\Application\View;
	use Jungle\Application\View\ViewStrategyInterface;
	use Jungle\Application\ViewInterface;
	use Jungle\Util\Specifications\Http\RequestInterface as HTTP_RequestInterface;
	use Jungle\Util\Specifications\Http\ResponseSettableInterface as HTTP_ResponseSettableInterface;

	/**
	 * Class Http
	 * @package Jungle\Application\Strategy
	 */
	class Http extends Strategy{

		protected $type = 'http';

		/**
		 * @param RequestInterface $request
		 * @return bool
		 */
		public function check(RequestInterface $request){
			return $request instanceof HTTP_RequestInterface;
		}

		/**
		 * @param ResponseInterface|HTTP_ResponseSettableInterface $response
		 * @param ViewInterface $view
		 * @internal param RendererInterface $renderer
		 */
		public function complete(ResponseInterface $response, ViewInterface $view){
			if($renderer = $view->getLastRenderer()){

				$content_mime_type = $renderer->getMimeType();
				$response->setContentType($content_mime_type);

				/** @var ViewStrategyInterface $view_strategy */
				$lastRendererAlias = $view->getLastRendererAlias();
				$view_strategy = $this->getShared('view_strategy');
				$view_strategy->complete($lastRendererAlias,$renderer,$response, $view);

			}
			parent::complete($response, $view);
		}

	}
}