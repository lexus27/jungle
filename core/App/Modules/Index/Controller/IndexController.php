<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 0:48
 */
namespace App\Modules\Index\Controller {
	
	use App\Model\User;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\Application\View\Renderer\TemplateEngine;
	use Jungle\Di\Injectable;
	use Jungle\Util\Specifications\Http\ResponseInterface;


	/**
	 * Class IndexController
	 * @package App\Modules\Index\Controller
	 */
	class IndexController extends Injectable{

		/**
		 * @return array
		 */
		public function getDefaultMetadata(){
			return [ 'private' => false ];
		}

		public function initialize(){}


		/**
		 * @return array
		 */
		public function indexMetadata(){
			return [
				'private'       => false,
				'hierarchy'     => true,
			    'native_render' => ['html'],
			];
		}

		/**
		 * @param Process $process
		 */
		public function indexAction(Process $process){
			
		}

		public function not_foundAction(Process $process){
			$this->response->setCode(404);
		}

		public function errorMetadata(){
			return [
				'hierarchy' => true,
			];
		}

		/**
		 * @param Process $process
		 */
		public function errorAction(Process $process){

			/** @var \Exception|\ErrorException $exception */
			$exception = $process->exception;
			// Logging
			$dirname = $this->application->getLogDirname();
			$logFile = $dirname . '/crash-log.json';
			if(file_exists($logFile) && ($content = file_get_contents($logFile))){
				$content = json_decode($content,true);
			}else{
				$content = [];
			}

			$message = $exception->getMessage();
			$filename = $exception->getFile();
			$line = $exception->getLine();
			$type = $exception instanceof \ErrorException?$exception->getSeverity():1;

			$hash = md5(serialize([$type,$message,$filename,$line]));
			if(isset($content[$hash])){
				$content[$hash]['stack']++;
			}else{
				$stack = 1;
				$date = date('Y-m-d');
				$content[$hash] = [
					'type'      => $type,
					'message'   => $message,
					'file'      => $filename,
					'line'      => $line,
					'stack'     => $stack,
					'date'      => $date,
					'time'      => time(),
				];
			}
			$content = json_encode($content,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
			file_put_contents($logFile, $content);
			// Logging end

			if( isset($this->response) && ($response = $this->response) instanceof ResponseInterface ){
				$response->setCode(500);
			}

		}

	}
}

