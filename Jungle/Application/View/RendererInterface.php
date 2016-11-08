<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.07.2016
 * Time: 3:08
 */
namespace Jungle\Application\View {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\ViewInterface;
	use Jungle\Util\CacheableInterface;
	use Jungle\Util\Named\NamedReadInterface;

	/**
	 * Class RendererInterface
	 * @package Jungle\Application
	 */
	interface RendererInterface extends CacheableInterface, ViewConfigurationInterface, NamedReadInterface{

		/**
		 * @param ViewInterface $view
		 * @return $this
		 */
		public function setView(ViewInterface $view);

		/**
		 * @return ViewInterface
		 */
		public function getView();

		/**
		 * @return string
		 */
		public function getType();

		/**
		 * @return string
		 */
		public function getMimeType();

		/**
		 * @return \mixed[]
		 */
		public function getVariables();

		/**
		 * @return \mixed[]
		 */
		public function getOptions();

		/**
		 * @return void
		 */
		public function initialize();

		/**
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param ViewInterface $view
		 * @param array $variables
		 * @param array $options
		 * @return string
		 */
		public function render(ProcessInterface $process, ViewInterface $view, array $variables = [], array $options = []);

		/**
		 * @return mixed
		 */
		public function getBaseDirname();

		/**
		 * @return mixed
		 */
		public function getCacheDirname();

	}
}

