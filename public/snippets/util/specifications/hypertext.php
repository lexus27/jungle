<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 21:31
 */

namespace Jungle\Util\Communication\Hypertext;

include '../../loader.php';

/**
 * Данный пример показывает как работает Парсинг и Отображение структуры документов
 * В данном примере так-же затрагивается работа с кодировками внутри компонента.
 *
 * При объектном формировании структуры Документа, кодировка у нас в порядке вещей нативная (default_charset).
 *
 * Рендеринг структуры в строку:
 * Процесс рендеринга: Документ в зависимости от заголовков кодируется.
 * Если контент является производным от ContentInterface
 * то внутри данного объекта может происходит изменение заголовков Документа до рендеринга.
 *
 * Результат рендеринга: Контент Документа полностью должен соответствовать заголовкам,
 * как и заголовки в зависимости от контента должны соответствовать требованиям типа контента по стандартам(если контента является ContentInterface).
 *
 * Парсинг(Синтаксический Анализ и Разбор):
 * При парсинге:  Документа из строки, так-же в зависимости от заголовков, происходит декодирование содержания.
 * После парсинга: Документ должен содержать уже декодированый, нормализованый контент соответствующий нативному представлению
 *
 * Стоит упомянуть здесь что Документ может быть Считан/Записан с разных источников.
 * Например [из строки и в строку] или [из потока / в поток].
 * При поточном считывании поддерживается приходящий заголовок Transfer-Encoding (не путать с Content-Transfer-Encoding)
 * Если он равен chunked, то произойдет разбор частей(чанков/chunks) прямо из потока,
 * Текст контента будет разобран из чанков и сформирован контент уже для дальнейшего разбора по заголовкам.
 *
 */



/** Кодировки  cp1253, koi8-r, iso-8859-5,  etc...*/
$DOCUMENT_CHARSET = 'iso-8859-5';

$document = new Document();
$document->setHeaders([
	'' => 'POST / HTTP/1.1',
	'Accept' => 'text/html',
	//'Content-Transfer-Encoding' => 'quoted-printable',
	//'Content-Transfer-Encoding' => 'base64',
	'Content-Type' => 'text/html; charset=' . $DOCUMENT_CHARSET
]);
$multipart = new Content\Multipart();

$raw_document = <<<'TAG'
Content-Type: text/plain
Content-Disposition: form-data; name=part1

Текст все-же в нужной кодировке, я думаю)
TAG;
$multipart->addPart(new Document($raw_document));

$raw_document = <<<'TAG'
Content-Type: text/plain
Content-Disposition: form-data; name=part2

Какойто второй текст.
TAG;
$multipart->addPart(new Document($raw_document));

$raw_document = <<<'TAG'
Content-Type: text/plain
Content-Disposition: form-data; name=part3

Какойто третий текст не похожий на второй
TAG;
$multipart->addPart(new Document($raw_document));
$document->setContent($multipart);


/**
 * По умолчанию сервер PHP отправляет клиенту charset=utf-8,
 * поэтому если отобразить контент с другой кодировкой, то читаемого текста мы не увидим.
 * Для проверки отображения мультибайтового контента в кодировках отличающихся от utf-8
 * можно указать в заголовках ответа charset=$DOCUMENT_CHARSET
 *
 * Так-же по умолчанию, файл написан в кодировке utf-8, весь видимый в файле текст. в том числе многобайтовый,
 * если указать в заголовках ответа charset=$DOCUMENT_CHARSET, то весь видимый в файле текст станет нечитаемым в браузере,
 * а мультибайтовый текст документов станет видимым.
 * Это можно проверить сменив кодировку страницы в настройках страницы в браузере.
 *
 * @code: ini_set('default_charset',$DOCUMENT_CHARSET);
 * Можно эмулировать эту опирацию, в ручную сменив кодировку страницы в настройках страницы в браузере.
 *
 */
//ini_set('default_charset',$DOCUMENT_CHARSET);

echo '<h1>Обращение со структурой Документа</h1>';
echo '<pre>';
echo "< Получим Партицию мультипарт контента, с Content-Disposition: name='part1' и выведем её\r\n";
echo "< Отобразим:\r\n\r\n";
$ct = $multipart->getPartByDispositionName('part1');
echo '<span style="margin-left:20px;display:inline-block;background:#eff8ff;">' . strtr((string) $ct, [ "\r\n" => "\r\n"]).'</span>';
echo '</pre>';


echo '<h1>Рендеринг структуры документа в строку</h1>';
echo '<pre style="display:inline-block;background:#eff8ff;">';
$inline_document = "$document";
echo $inline_document;
echo '</pre>';



echo '<h1>Парсинг документа из строки, и проверка целостности дальнейшего рендеринга</h1>';
$document = new Document($inline_document);
echo '<pre style="display:inline-block;background:#eff8ff;">';
echo $inline_re_document = $document;
echo '</pre>';

$hash = md5($inline_document);
$hash_re = md5($inline_re_document);

echo '<h2>Разница <u>render(structure) > parse(rendered) > render(parsed_structure)</u>: '.strcmp($inline_document, $inline_re_document).'</h2>';
echo '<h2>Сравните хеши</h2>';
echo '<div>Рендеринг 1: '.$hash.'</div>';
echo '<div>Рендеринг 2: '.$hash_re.'</div>';