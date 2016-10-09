Hyper Text
==========

###### Спецификация HTT

### Сводка
* Двух направленная реакция Заголовки-Контент
    * Заголовки влияют на формат контента
    * Контент влияет на заголовки (по умолчанию на `Content-Length`)
        > Если контент является производным`ContentInterface` то может влиять на любой спектр заголовков перед отображением
* Кодирование/Декодирование контента по заголовкам
    > Документ в строковом представлении, полностью закодирован в соответствии с заголовками.
* Чтение из разных источников (по умолчанию строка)
    > При чтении из потоков поддерживается `Transfer-Encoding: chunked`
* Запись в разные источники (по умолчанию строка)
* Обращаться к заголовкам следует по `Camelize` стилю.
* Инструменты для работы с заголовками
    * Нормализация ключей заголовков (`Camelize` стиль)
    * Разбор значения
        
        
            ['value' => null, 'params' => [], 'elements' => []]
    


### Рендеринг структуры в строку
Процесс рендеринга: Документ в зависимости от заголовков кодируется.
Если контент является производным от ContentInterface
то внутри данного объекта может происходит изменение заголовков Документа до рендеринга.

### Результат рендеринга
Контент Документа полностью должен соответствовать заголовкам, как и заголовки в зависимости от контента должны соответствовать требованиям типа контента по стандартам(если контента является ContentInterface).

### Парсинг(Синтаксический Анализ и Разбор)
При парсинге:  Документа из строки, так-же в зависимости от заголовков, происходит декодирование содержания.
После парсинга: Документ должен содержать уже декодированый, нормализованый контент соответствующий нативному представлению

### Возможности разных источников

Стоит упомянуть здесь что Документ может быть Считан/Записан с разных источников.
Например [из строки и в строку] или [из потока / в поток].
При поточном считывании поддерживается приходящий заголовок Transfer-Encoding (не путать с Content-Transfer-Encoding)
Если он равен chunked, то произойдет разбор частей(чанков/chunks) прямо из потока, Текст контента будет разобран из чанков и сформирован контент уже для дальнейшего разбора по заголовкам.


## Примеры

    <?php
    namespace Jungle\Util\Specifications\Htt;
    
    include '../../loader.php';

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
    Content-Type: image/gif
    Content-Disposition: attachment; filename=gif1.gif; name=part1
    
    Текст все-же в нужной кодировке, я думаю)
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
    
    echo '<h3>Обращение со структурой Документа</h3>';
    echo '<pre style="font-family: Lucida Console">';
    echo ">> Получим Партицию мультипарт контента, с Content-Disposition: name='part1' и выведем содержание\r\n";
    echo '<br/>';
    echo $multipart->getPartByDispositionName('part1')->getContent();
    echo '</pre>';
    
    
    echo '<h3>Рендеринг структуры документа в строку</h3>';
    echo '<pre style="font-family: Lucida Console">';
    $inline_document = "$document";
    echo $inline_document;
    echo '</pre>';
    
    $hash = md5($inline_document);
    
    echo '<h3>Парсинг документа из строки, и проверка целостности дальнейшего рендеринга</h3>';
    $document = new Document($inline_document);
    echo '<pre style="font-family: Lucida Console">';
    echo $inline_re_document = $document;
    echo '</pre>';
    
    $hash_re = md5($inline_re_document);
    
    echo '<h2>Разница <u>render(structure) > parse(rendered) > render(parsed_structure)</u>: '.strcmp($inline_document, $inline_re_document).'</h2>';
    echo '<h2>Сравните хеши</h2>';
    echo '<div>Рендеринг 1: '.$hash.'</div>';
    echo '<div>Рендеринг 2: '.$hash_re.'</div>';