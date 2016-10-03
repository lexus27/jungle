<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 0:47
 */
namespace sequence;

use Jungle\Util\Communication\Sequence;
use Jungle\Util\Communication\Sequence\Specification\Smtp;

include '../../loader.php';

/**
 * Example with Smtp (Jungle\Util\Communication\Sequence\Specification\Smtp)
 */

$specification = new Smtp();
$sequence = $specification->createSequence();
$sequence->setSpecification($specification);
$sequence->setConfig([
	'params_merge' => true,

	'scheme'       => 'ssl',
	'host'         => 'smtp.mail.ru',
	'port'         => 465,

	'login'        => null,
	'password'     => null,

	'mail_from'    => null,
	'recipient'    => null,

	'data'         => null,
	'size'         => null,
]);
$sequence->setSequence([
	'hello',
	'auth',
	'mail_from',
	'recipient',
	'data'
]);
$sequence->run();