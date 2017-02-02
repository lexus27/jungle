Jungle PHP Framework(JF)
========================
v0.1.6
Фреймворк придерживается следующим общим стандартам:
    
* Hierarchy-MVC(hmvc) Архитектура
* Для контроля доступа используются стандарты _Attribute Based Access Control (ABAC)_
* ORM Поддерживающая _global reusable objects_, _наследование схем_, _динамические поля `класса схемы` и `связей`_
* Генерирование ссылок по данным маршрутизации в mca-системе

#### Module Controller Action (MCA)

Система скелета приложения, имеет ряд целей для обеспечения удобства разработчика, этот субъект является самым важным в проектировании проложения

## Установка
###### Git
    git clone https://github.com/lexus27/Jungle.git
###### Composer
    composer require lexus27/jungle
    
##### Структура файловой системы WEB-Сервера
Структура файловой системы может быть индивидуальна, но базовой для начала работы можно считать следующую

    /core
        /App
            /_cache (reserved! for application auto generate)
            /_log (reserved! for application auto generate)
            /Model
            /Modules
            /Services
            /Strategies
            /Views
            Application.php
        /Jungle (Примерное расположение фреймворка)
    /public_html
        /assets (custom use)
        /index.php
        /.htaccess - (if apache webserver used)
        
* **`/core`** 
    Папка для хранения источников: приложений(/App), библиотек и фреймворка(/Jungle)


* **`/core/App/`**
    Служебная папка приложения (Совместима с Namespace PSR-4 и Автозагрузкой)


* **`/core/App/_log/`**
    Автогенерируемая папка зарезервированая под системные логи 


* **`/core/App/_cache/`**
    Автогенерируемая папка зарезервированая под всяческий файловый кеш


* **`/core/App/Model/`**
    Зарезервированая папка для моделей ORM, модели могут быть определены в пространстве имен `namespace App/Model`, поэтому название папки не имеет технического значения и носит только смысловой характер


* **`/core/App/Modules/`**
    Папка под организацию структуры Контроллеров приложения
    
    
* **`/core/App/Services/`**
    Зарезервированая папка для Переопределенных или индивидуальных компонентов JF, используемых в приложении (Носит смысловой характер)
    
    
* **`/core/App/Strategies/`**
    Папка под стратегии запроса, в ней хранятся стратегии в виде классов с названием самой стратегии.
    
    
* **`/core/App/Application.php`**
    Класс скелет приложения, в нем могут быть переопределены служебные относительные пути например (`Strategies` или `Modules`)

* **`/core/Jungle`**
    Примерное расположение фреймворка (_Далее примеры кода будут опираться на это расположение_)

* **`/public_html`**
    Корень веб сервера, здесь хранятся файлы которые доступны публично. в том числе Точка входа в приложение
    
    
* **`/public_html/index.php`**
    Точка входа в приложение, ни что от этого файла не зависит, в нем осуществляется подключение Автозагрузчика и Приложения



##### Точка входа (/public_html/index.php)

> Традиционно Для работы ЧПУ, Web-Сервер должен поддерживать Перенаправление (mod_rewrite) 
    
    
    <?php
    //use absolute path
    
    
    /** 
     * 0. Optional defines contsants for comfort 
     * -----------------------------------------
     */
    
    // similarly realpath('../../core/Jungle/Jungle')
    !defined('JUNGLE_DIRNAME') && 
        define('JUNGLE_DIRNAME', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'core' . DIRECTROY_SEPARATOR . 'Jungle' . DIRECTORY_SEPARATOR . 'Jungle');
    
    // similarly realpath('../../core/App')
    !defined('APP_DIRNAME') && 
        define('APP_DIRNAME', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'App');
    
    /** 
     * 1. Include loader file
     * ----------------------
     */
     
    include JUNGLE_DIRNAME . DIRECTORY_SEPARATOR . 'Loader.php';
    
    /** 
     * 2. Autoloader registers namespaces Jungle/ and your App/ 
     * --------------------------------------------------------
     */
      
    $loader = \Jungle\Loader::getDefault();
    $loader->registerNamespaces([
    	'Jungle'    => JUNGLE_DIRNAME,
    	'App'       => APP_DIRNAME
    ]);
    $loader->register();
    
    /** 
     * 3. Application instantiate and run
     */
     
    $app = new \App\Application($loader);
    $response = $app->handle(\Jungle\Http\Request::getInstance());
    $response->send(); // fully auto send output
    
