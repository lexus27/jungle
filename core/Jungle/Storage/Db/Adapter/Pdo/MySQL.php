<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 12:53
 */
namespace Jungle\Storage\Db\Adapter\Pdo {

	use Jungle\Storage\Db\Adapter\Pdo;

	/**
	 * Class MySQL
	 * @package Jungle\Storage\Db\Adapter\Pdo
	 */
	class MySQL extends Pdo{

		/** @var string */
		protected $driverType = 'mysql';

		/** @var string */
		protected $dialectType = 'MySQL';

	}
}

