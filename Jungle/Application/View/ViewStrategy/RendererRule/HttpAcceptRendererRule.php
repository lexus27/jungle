<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.07.2016
 * Time: 1:38
 */
namespace Jungle\Application\View\ViewStrategy\RendererRule {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\Application\View\ViewStrategy\RendererRule;
	use Jungle\Application\ViewInterface;

	/**
	 * Class HttpAcceptRendererRule
	 * @package Jungle\Application\View\ViewStrategy\RendererRule
	 */
	class HttpAcceptRendererRule extends RendererRule{

		protected $pieces = [];

		/**
		 * HttpAcceptRendererRule constructor.
		 * @param array|string $pieces
		 */
		public function __construct($pieces){
			$this->pieces = !is_array($pieces)?[$pieces]:$pieces;
		}

		/**
		 * @param RequestInterface|\Jungle\Util\Communication\HttpFoundation\RequestInterface $request
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param ViewInterface $view
		 * @return bool
		 */
		public function __invoke(RequestInterface $request, ProcessInterface $process, ViewInterface $view){
			$accept = $request->getHeader('Accept');
			foreach($this->pieces as $needle){
				if(stripos($accept, $needle)!==false){
					return true;
				}
			}
			return false;
		}
	}
}

