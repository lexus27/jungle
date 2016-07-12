<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.07.2016
 * Time: 20:15
 */
namespace Jungle\Application\View {
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\ViewInterface;

	/**
	 * Interface RendererMatcherInterface
	 * @package Jungle\Application\View
	 */
	interface RendererMatcherInterface{

		/**
		 * @param $render_alias
		 * @param callable $rule
		 * @param int $priority
		 * @return $this
		 */
		public function addRule($render_alias, callable $rule, $priority = 0);

		/**
		 * @param $render_alias
		 * @param int $priority
		 * @return $this
		 */
		public function setPriority($render_alias, $priority = 0);



		/**
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string
		 */
		public function __invoke(ProcessInterface $process, ViewInterface $view);

	}
}

