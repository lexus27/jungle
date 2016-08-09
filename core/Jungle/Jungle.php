<?php
/**
 *
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.11.2015
 * Time: 22:18
 *
 * @RegExp ViewStrategy expression pattern aliases
 *
 * TODO RegExp fast definition pattern (Как в роутере Phalcon)
 * TODO RegExp Wrapper handling, OOP Access to matching, callable replacing support
 * preg_replace
 * preg_match
 * preg_replace_callback
 *
 * Основная парадигма Jungle - Это детализация и пре-процессинг того что машина может знать заранее
 * (
 *      например поведение на клиенте возможные входящие HTTP запросы(
 *      непосредственно создаваемые контроллеры и обратная сторона это
 *      то место на клиенте откуда выполнится запрос к контроллеру
 *      (
 *          X====X Связь управления где доминирует Тот(front|back)End который
 *          целом больше всего может знать о системе для пре-процессинга
 *      )
 * )
 *
 * Контекст строительства приложения
 * Jungle библиотека является продуктом двух контекстов (Клиент - Сервер)
 *
 * @J-Server - это генератор клиента, его конфигуратор, конструктор , на его плечах тяжелый препроцессинг сущностей ,
 *     тяжелой логики и других конечных компонентов использующихся клиентским сервером, которые не хотелось бы
 *     выполнять каждый раз при продуктивной работе целевого приложения....
 *
 * J-Server будет оснащен всеми инструментами для проектирования, программирования, настройки и валидации сущностей
 *     парадигмы приложения J-Client, его пользовательский интерфейс должен быть направлен на тестирования, трекинг и
 *     поддержку обновлений с J-Client
 *
 * J-Server имеет БД J-Client`ов которая содержит настройки пакетов J-Client
 * и непосредственно по этим настройкам и генерируется набор для развертывания TargetTable J-Client на WebServer
 *
 * Таким способом программа должна забрать на свои плечи, то многое над чем ранее приходилось работать вручную
 *
 * @J-Client - целевая серверная платформа сконфигурированная для продуктивной работы.
 * Легкое исполнение благодаря умной генерации.
 * Все части такого сервера уже отпрепроцессились -
 * в отличие от J-Сервера, J-Клиент хранит уже выполненые и не требуемые обработки части.
 *
 * Smart\Keyword с их производными, HTMLTag, HTMLAttribute, WEBEngine, Value\Color Palettes.
 * Logic\Operator.
 * TypeHint\TypeChecker.
 * и другие сущности ООП препроцессинга
 *
 * Части которые могут хранится и изменятся в БД J-Server, они же на J-Client будут хранится в 1 файле для всех
 *     сущностей, или будут вшиты(заменены на complete processing данные) в отдельных клочках перегенерированного
 *     J-Server`ом кода доступ для изменения на Jungle Client к ним исключен, при изменении данных на JServer, J-Client
 *     при обновлении запросит релиз данных и перешьет свои файлы на новый лад.
 *
 * Тоесть отсуда ясно что J-Client будет содержать как минимум только ту базу данных которая
 * будет изменятся и использоваться в продакшене, это целевые структуры данных...
 *
 * Остальные же данные необходимые для обеспечения поддержки парадигмы работы J-Client будут хранится статически.
 */
namespace Jungle;
class Jungle {

	/** @var float[]  */
	protected static $_time_measurement = [];

	protected static $_memory_measurement = [];

	/**
	 * @param $time_key
	 */
	public static function startTime($time_key){
		self::$_time_measurement[$time_key] = microtime(true);
	}

	/**
	 * @param $time_key
	 * @param null $format
	 * @return bool|mixed|string
	 */
	public static function endTime($time_key, $format = null){
		if(isset(self::$_time_measurement[$time_key])){
			$time =microtime(true) - self::$_time_measurement[$time_key];
			unset(self::$_time_measurement[$time_key]);
			return $format ? sprintf($format,$time) : $time;
		}else{
			return false;
		}
	}

	/**
	 * @param $key
	 */
	public static function startMemoryMeasure($key){
		self::$_memory_measurement[$key] = memory_get_usage();
	}

	/**
	 * @param $key
	 * @param null $format
	 * @return float MiB
	 */
	public static function endMemoryMeasure($key, $format = null){
		if(isset(self::$_memory_measurement[$key])){
			$memory = memory_get_usage() - self::$_memory_measurement[$key];
			unset(self::$_memory_measurement[$key]);
			$memory = $memory / 1024 / 1024;
			return $format ? sprintf($format,$memory) : $memory;
		}else{
			return false;
		}
	}

	/**
	 * @return int MiB
	 */
	public static function getMemoryUsage(){
		return memory_get_usage() / 1024 / 1024;
	}

}



/**
 *
 * Прием мультимедийных файлов на сервере
 * Отправка мультимедийных файлов, потоков на клиент
 * Отправка сообщений во внешней среде
 * Работа с файловой системой
 * Коммутирование (Работа с потоками, отправкой, меж-сетевой транспорт)
 * Работа с коллекциями и объектами данных ICollection IDataMap
 * Регулярные выражения -> последовательная работа с результатами -> преобразование данных в масках
 * Валидация типов данных(TypeHinting) Расширенная: DateTime, Color, Measurement Value(Money, Distance, and them), Js Script, PHP Script
 * Фильтрация данных (Filter), или расширенное приведение к типам DateTime > UnixTimestamp,
 * Поддержка сессий
 * Регистрация
 * Роутинг (MVC)
 */

