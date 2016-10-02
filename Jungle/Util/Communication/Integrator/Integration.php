<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 12:30
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Class Integration
	 * @package Jungle\Util\Communication\Integrator
	 */
	class Integration extends Section{

		public static function __construct(){
			$params = [
				'name'      => 'vk',
				'host'      => 'api.vk.com',
				'method'    => 'post',
				'combiner'  => 'implode-uri',
				'uri'       => '/method',

				'sections' => [
					'users' => [
						'uri' => '/users',
						'methods' => [
							'get' => [
								'uri' => '.get',
								'params' => [
									'user_ids'  => [],
									'fields'    => [],
									'name_case' => 'nom'
								],
							]
						]
					],
					'auth' => [

					]
				],



			];
		}

		/**
		 * @param Method $method
		 */
		public function onSuccess(Method $method){

		}

	}
}

