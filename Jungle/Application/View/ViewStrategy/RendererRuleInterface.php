<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.07.2016
 * Time: 21:07
 */
namespace Jungle\Application\View\ViewStrategy {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\Application\View\RendererInterface;
	use Jungle\Application\View\ViewConfigurationInterface;
	use Jungle\Application\View\ViewStrategy;
	use Jungle\Application\View\ViewStrategyInterface;
	use Jungle\Application\ViewInterface;

	/**
	 * Interface RendererRuleInterface
	 * @package Jungle\Application\View\ViewStrategy
	 */
	interface RendererRuleInterface extends ViewConfigurationInterface{

		/**
		 * @param RequestInterface $request
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @return bool
		 */
		public function __invoke(RequestInterface $request, ProcessInterface $process, ViewInterface $view);

		/**
		 * @return \mixed[]
		 */
		public function getOptions();

		/**
		 * @return \mixed[]
		 */
		public function getVariables();


		/**
		 * @param RendererInterface $renderer
		 * @param ViewStrategyInterface $view_strategy
		 * @param ViewInterface $view
		 * @return string|null
		 */
		public function preRender(RendererInterface $renderer, ViewStrategyInterface $view_strategy, ViewInterface $view);

		/**
		 * @param RendererInterface $renderer
		 * @param ViewStrategyInterface $view_strategy
		 * @param ViewInterface $view
		 * @return string|null
		 */
		public function postRender(RendererInterface $renderer, ViewStrategyInterface $view_strategy, ViewInterface $view);

	}
}

