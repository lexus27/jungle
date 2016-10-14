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
/**
 * 1. Создаем менеджер
 */
$messenger = new Messenger\Mail\SMTP\SMTP([

	'host'              => 'smtp.mail.ru',
	'port'              => 465,
	'transport'         => 'ssl',

	'auth'              => '<LOGIN:PASS>',
	'sender'            => 'NAME <EMAIL>',

	'agent'             => 'JF-Messenger/1.0',
	'interval'          => 10,
	'max_destinations'  => 30,
]);

/**
 * 2. Готовим комбинацию рассылки
 */
$combination = new Combination();

/**
 * 3. Задаем сообщение для рассылки
 */
$message = new Message();
$message->setSubject('Тема для рассылки');
$message->setContent('Контент рассылки');
$combination->setMessage($message);

/**
 * 4. Добавляем получателей
 */
$contact = Contact::getContact('NAME <EMAIL>');
$combination->addDestination($contact);


/**
 * 5. Отправляем
 */
$messenger->send($combination);