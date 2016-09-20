<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 16:00
 */
namespace Jungle\Util\Data\Validation\Rule {
	
	use Jungle\Util\Data\Validation\Rule;

	/**
	 * Class Unique
	 * @package Jungle\Util\Data\Validation\Rule
	 *
	 * TODO
	 * TODO Данное правило не применимо на уровнях предварительной проверки (До непосредственного запроса в хранилище)
	 * TODO Но есть идея отслеживать Исключения из Операции выполнения в хранилище,
	 * TODO таким образом, чтобы Правило смогло отловить исключение и сделать как бы отложенный вердик на поздних этапах валидации
	 * TODO Возможно!
	 * TODO из метода _expertize можно вернуть функцию которая по смыслу
	 * TODO будет участвовать при отлавливании исключения на поздних этапах
	 * TODO Это потребует дополнительной обвязки в Record и привязки изначально данного правила к Определенному полю
	 * TODO Тоесть замыкание будет обернуто в определенный объект в котором будет указано название поля
	 * TODO
	 * TODO Или можно изменить поведение правил и добавить режим отложенной проверки и запускать её во время получения исключения
	 * TODO ответ от _expertize эквивалентный FALSE будет означать что данное правило схватило исключение и оно соответствует
	 * TODO насущной ошибке
	 *
	 */
	abstract class Unique extends Rule{
		
		/**
		 * @param $value
		 * @return mixed
		 */
		protected function _expertize($value){
			// TODO: Implement _expertize() method.
		}

	}
}

