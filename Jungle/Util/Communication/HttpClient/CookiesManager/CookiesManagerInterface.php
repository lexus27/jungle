<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.10.2016
 * Time: 20:03
 */
namespace Jungle\Util\Communication\HttpClient\CookiesManager {

	use Jungle\Util\Communication\HttpClient\Request;
	use Jungle\Util\Communication\HttpFoundation\Cookie;
	use Jungle\Util\Communication\HttpFoundation\CookieInterface;
	
	/**
	 * Interface CookiesManagerInterface
	 * @package Jungle\Util\Communication\HttpClient
	 */
	interface CookiesManagerInterface{
		
		/**
		 * @param Request $request
		 * @return \Jungle\Util\Communication\HttpFoundation\Cookie[]
		 */
		public function matchSuitable(Request $request);

		/**
		 * @param CookieInterface $cookie
		 */
		public function storeCookie(CookieInterface $cookie);

	}
}

