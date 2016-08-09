<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:16
 */
namespace Jungle\Application {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\View\RendererInterface;
	use Jungle\Application\View\ViewConfigurationInterface;

	/**
	 * Interface ViewInterface
	 * @package Jungle\Application\View
	 */
	interface ViewInterface extends ViewConfigurationInterface{

		/**
		 * @param $alias
		 * @param RendererInterface $renderer
		 * @return $this
		 */
		public function setRenderer($alias, RendererInterface $renderer);

		/**
		 * @param $alias
		 * @return RendererInterface|null
		 */
		public function getRenderer($alias);

		/**
		 * @param $alias
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param array $variables
		 * @param array $options
		 * @return string
		 */
		public function render($alias, ProcessInterface $process, array $variables = [], array $options = []);

		/**
		 * @return RendererInterface
		 */
		public function getLastRenderer();

		/**
		 * @return string
		 */
		public function getLastRendererAlias();

		/**
		 * @return mixed[]
		 */
		public function getVariables();

		/**
		 * @return mixed[]
		 */
		public function getOptions();

		/**
		 * @param $dirname
		 * @return $this
		 */
		public function setBaseDirname($dirname);

		/**
		 * @return string
		 */
		public function getBaseDirname();

		/**
		 * @param $dirname
		 * @return $this
		 */
		public function setCacheDirname($dirname);

		/**
		 * @return string
		 */
		public function getCacheDirname();

		/**
		 * @return string[]
		 */
		public function getRendererAliases();

		/**
		 * @param RendererInterface $renderer
		 * @return string
		 */
		public function getRendererName(RendererInterface $renderer);


	}
}

