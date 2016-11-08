<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 0:47
 */
namespace Jungle\Util\Communication\Sequence;

use Jungle\Util\Communication\ApiInteractingStream\ExtraCombination;
use Jungle\Util\Communication\ApiInteractingStream\SmtpApi;

include '../../loader.php';

/**
 * Example with Smtp (Jungle\Util\Communication\Sequence\Specification\Smtp)
 */

$api = new SmtpApi();
$combination = new ExtraCombination($api);
$combination->setDefaultParams([
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
$combination->setList([
	'hello',
	'auth',
	'mail_from',
	'recipient',
	'data'
]);
$combination->run();