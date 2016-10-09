<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 16:00
 */

namespace Jungle;

use Jungle\Messenger\Combination;
use Jungle\Messenger\Mail\Contact;
use Jungle\Messenger\Mail\Message;

include './loader.php';
$messenger = new Messenger\Mail\SMTP\SMTP([
	'host'              => 'smtp.mail.ru',
	'port'              => 465,
	'transport'         => 'ssl',
	'auth'              => '<lexus.1995@mail.ru:@JungleTechLabs2016@>',
	'sender'            => 'Алексей Кутузов <lexus.1995@mail.ru>',
	'agent'             => 'Jungle PHP Messenger',
	'interval'          => 10,
	'max_destinations'  => 30,
]);

$combination = new Combination();

$message = new Message();
$message->setSubject('Тема для рассылки');
$message->setContent('Контент рассылки');

$combination->setMessage($message);

$contact = Contact::getContact('Алексей Кутузов <lexus27.khv@gmail.com>');
$combination->addDestination($contact);


$messenger->send($combination);