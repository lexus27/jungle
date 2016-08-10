<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.07.2016
 * Time: 19:01
 */
namespace Jungle\Application\View {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\Application\ResponseInterface;
	use Jungle\Application\View\ViewStrategy\RendererRule;
	use Jungle\Application\ViewInterface;

	/**
	 * Interface ViewStrategyInterface
	 * @package Jungle\Application\View
	 */
	interface ViewStrategyInterface extends ViewConfigurationInterface{


		/**
		 * @param $alias
		 * @param RendererInterface $renderer
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string|null
		 */
		public function preRender($alias, RendererInterface $renderer, ProcessInterface $process, ViewInterface $view);

		/**
		 * @param $alias
		 * @param RendererInterface $renderer
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string|null
		 */
		public function postRender($alias, RendererInterface $renderer, ProcessInterface $process, ViewInterface $view);

		/**
		 * @param $rendererAlias
		 * @param RendererInterface $renderer
		 * @param ResponseInterface $response
		 * @param ViewInterface $view
		 */
		public function complete($rendererAlias, RendererInterface $renderer, ResponseInterface $response, ViewInterface $view);

		/**
		 * @param RequestInterface $request
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string
		 */
		public function match(RequestInterface $request, ProcessInterface $process, ViewInterface $view);

		/**
		 * @param $alias
		 * @param ViewInterface $view
		 * @param RendererInterface $renderer
		 * @param ProcessInterface $process
		 * @param array $variables
		 * @param array $options
		 * @return mixed
		 */
		public function render($alias, ViewInterface $view, RendererInterface $renderer, ProcessInterface $process, array $variables = [ ], array $options = [ ]);


		/**
		 * @param $alias
		 * @param callable $rule
		 * @return $this
		 */
		public function addRule($alias, callable $rule);

		/**
		 * @param $alias
		 * @return RendererRule|callable
		 */
		public function getRule($alias);

	}
}

