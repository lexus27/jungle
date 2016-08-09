<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.07.2016
 * Time: 15:29
 */
namespace Jungle\Application {

	/**
	 * Дополнительно требуется обеспечить:
	 *
	 * Возможность настраивать реакцию на запрет доступа к Контроллерам
	 * NOT_FOUND | ACCESS_DENIED
	 *
	 * Возможность настраивать возможность вывода запрещенных объектов в определенных листингах
	 *      Если можно: То указывать как отображать такие объекты в конкретном листинге
	 *      Если нельзя:
	 *          То использовать предикат запроса для получения только разрешенных
	 *          Или проверять в цикле каждый объект
	 */



	/**
	 * Interface AccessInterface
	 * @package Jungle\Application
	 */
	interface AccessInterface{

		const ACTION_CREATE         = 'create';

		const ACTION_READ           = 'read';

		const ACTION_UPDATE         = 'update';

		const ACTION_DELETE         = 'delete';


		const UI_DISPLAY_SINGLE     = 'single';

		const UI_DISPLAY_LISTING    = 'listing';

		/**
		 * @param $reference
		 * @return bool
		 */
		public function hasControlPermission($reference);

		/**
		 * @param $objectKey
		 * @param $action
		 * @return bool
		 */
		public function hasCrudPermission($objectKey, $action);

		/**
		 * @param $object
		 * @param $action
		 * @return bool
		 */
		public function hasObjectPermission($object, $action);

		/**
		 * @param $objectKey
		 * @param array $mask
		 * @return array|false
		 */
		public function getObjectLoadPredicates($objectKey, array $mask);


		/**
		 * @param $objectKey
		 * @param $condition
		 */
		public function accessCollection($objectKey, $condition);

		/**
		 * @param $objectKey
		 * @param $condition
		 */
		public function accessObject($objectKey, $condition);

		/**
		 * @param $reference
		 * @param $params
		 */
		public function accessControl($reference, $params);

		/**
		 * @param $objectKey
		 * @param $displayType
		 * @return bool
		 */
		public function hasUIVisibility($objectKey, $displayType);

		/**
		 * @param $objectKey
		 * @param $listingName
		 * @return mixed
		 */
		public function visibleObjectInListing($objectKey, $listingName);

	}
}

