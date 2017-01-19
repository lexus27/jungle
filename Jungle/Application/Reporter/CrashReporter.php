<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.10.2016
 * Time: 19:03
 */
namespace Jungle\Application\Reporter {

	use Jungle\Application;

	/**
	 * Class CrashReporterInterface
	 * @package Jungle\Application\Reporter
	 */
	class CrashReporter implements ExceptionReporterInterface{

		/** @var Application  */
		protected $application;

		/** @var  Logger */
		protected $logger;

		/** @var int  */
		protected $max_file_size = 5000000;

		/**
		 * CrashReporter constructor.
		 * @param Application $application
		 * @param Logger $logger
		 */
		public function __construct(Application $application, Logger $logger = null){
			$this->application = $application;
			$this->logger = $logger;
		}

		/**
		 * @param \Exception $exception
		 */
		public function report(\Exception $exception){
			// Logging
			$dirname = $this->application->getLogDirname();

			$i = 1;
			do{
				$logFile = $dirname . '/crash/report-list-'.$i.'.json';
				if(!file_exists($logFile)) break;
				$i++;
			}while(filesize($logFile) > $this->max_file_size);

			$content = null;
			if(file_exists($logFile) && ($content = trim(file_get_contents($logFile)))){
				$content = @json_decode($content,true);
			}
			if(!$content) $content = [];

			$message    = $exception->getMessage();
			$filename   = $exception->getFile();
			$line       = $exception->getLine();
			$type       = $exception instanceof \ErrorException?$exception->getSeverity():1;



			$hash = md5(serialize([$type,$message,$filename,$line]));
			if(isset($content[$hash])){
				$c = $content[$hash];
				unset($content[$hash]);
				$time = time();
				$date = date('Y-m-d (H:i:s)',$time);
				$c['stack']++;
				if($c['trace'] !== false){
					if($exception instanceof \ErrorException){
						$trace = null;
					}else{
						$trace = $exception->getTrace();
					}
					$c['trace'] = $trace;
				}
				$c['date'] = $date;
				$c['time'] = $time;
				$content[$hash] = $c;
			}else{
				if($exception instanceof \ErrorException){
					$trace = null;
				}else{
					$trace = $exception->getTrace();
				}
				$stack = 1;
				$time = time();
				$date = date('Y-m-d (H:i:s)',$time);
				$content[$hash] = [
					'type'      => $type,
					'message'   => $message,
					'file'      => $filename,
					'line'      => $line,
					'trace'     => $trace,
					'stack'     => $stack,
					'date' => $date,
					'time' => $time,
					'first_date' => $date,
					'first_time' => $time,
				];
			}
			$string = json_encode($content,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);

			if($string===false){
				if(json_last_error() === 6){
					$content[$hash]['trace'] = false;
					$string = json_encode($content,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
				}
			}

			if(!is_dir($dirname = dirname($logFile))){
				mkdir($dirname,0555,true);
			}
			file_put_contents($logFile, $string);

			if($this->logger){
				$data = [
					'type'    => $type,
					'message' => $message,
					'file'    => $filename,
					'line'    => $line,
				];
				$this->logger->write('[APPLICATION] CrashReport: '.var_export($data,true), Logger::LOG_CRASH);
			}

		}

	}
}

