<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.11.2016
 * Time: 16:47
 */
namespace Jungle\Data\Record {

	/**
	 * Class CollectionFusion
	 * @package Jungle\Data\Record
	 * Слитая коллекция
	 * Отправляет запрос в хранилище используя джоины ,
	 * получает коллекцию записей после чего разбивает каждую, на несколько объектов,
	 * далее уже образуется слитая запись
	 * слитая запись имеет запись по умолчанию,
	 * и промежуточные записи, которые могут быть не полными
	 * к полю промежуточной записи можно получить доступ {mediate_name}.{field_name}
	 */
	class CollectionFusion{
		
	}
}

