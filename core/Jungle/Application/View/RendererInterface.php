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
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

	/**
	 * Class RendererInterface
	 * @package Jungle\Application
	 */
	interface RendererInterface{

		/**
		 * @return mixed
		 */
		public function getType();

		/**
		 * @return mixed
		 */
		public function initialize();

		/**
		 * @param ProcessInterface $process
		 * @return string
		 */
		public function render(ProcessInterface $process);

	}
}

