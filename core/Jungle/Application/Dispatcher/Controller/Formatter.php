<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.05.2016
 * Time: 18:29
 */
namespace Jungle\Application\Dispatcher\Controller {

	use Jungle\DataOldRefactoring\DataMap;
	use Jungle\Util\Data\Map\Collection;

	/**
	 * Class Formatter
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	abstract class Formatter{

		// Опишу для начала JSON

		/** @var  string */
		protected $name;


		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $data
		 * @param array $options
		 */
		public function format($data,array $options = []){


			$data = [
				'value' => null,
				'notices' => [
					'errors' => [],
					'events' => [],
				]
			];

			if(is_array($data)){



			}elseif(is_object($data)){



			}

			$options = array_replace([

			],$options);

			if($data instanceof Collection){

			}elseif($data instanceof DataMap){

			}



			/**
			 *
			 * Типы форматирования:
			 * 		абсолютное
			 * 			При абсолютном форматировании в формат включаются все ошибки и уведомления
			 * 		локальное
			 * 			При локальном форматировании в формат ничего кроме объектов не включается
			 *
			 */
			$object = [

				'errors' => [
					[
						'message' => '',
						'type'
					]
				]

			];

			$object = [
				'success' => true,
				'total' => 790, // Общее количество объектов в стеке минуя лимиты
				'count' => 100, // Количество по запросу лимитов
				'collection' => []
			];

			/**
			 * Уведомления
			 */
			$object = [
				'flash' => [
					'errors' => [],
					'notices' => []
				]
			];

			$object = [

				'success' => true|false,
				'object' => []

			];


			$object = [

				'success' => true,
				'collection' => [],
				'count'

			];



		}




	}
}

