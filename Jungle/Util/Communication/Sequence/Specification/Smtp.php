<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 22:06
 */
namespace Jungle\Util\Communication\Sequence\Specification {
	
	use Jungle\Util\Communication\Connection\Stream\Socket;
	use Jungle\Util\Communication\ConnectionInteractionInterface;
	use Jungle\Util\Communication\ConnectionInterface;
	use Jungle\Util\Communication\Sequence;
	use Jungle\Util\Communication\Sequence\ProcessSequenceInterface;
	use Jungle\Util\Communication\Sequence\Specification;

	/**
	 * Class Smtp
	 * @package Jungle\Util\Communication\Stream\Specification
	 */
	class Smtp extends Specification{


		/**
		 * Smtp constructor.
		 */
		public function __construct(){
			$this->command('start',[
				'rules' => [[
					'check'     => [220],
					'message'   => 'Error connect to SMTP server',
					'negated'   => true
				]]
			]);
			$this->command('hello',[
				'definition' => 'EHLO {host}',
				'rules' => [[
					'code' => 250,
				    'message' => 'SMTP Hello error'
				]]
			]);
			$this->bundle('auth',[[
				'definition' => 'AUTH LOGIN',
				'rules' => [
					['code' => 334, 'message' => 'Authentication error(START)']
				]
			],[
				'definition' => '{login}',
				'rules' => [
					['code' => 334, 'message' => 'Authentication error(login)']
				]
			],[
				'definition' => '{password}',
				'rules' => [
					['code'=> 235, 'message' => 'AuthenticationMissed']
				]
			]]);
			$this->command('mail_from',[
				'definition' => 'MAIL FROM:<{from}> SIZE={size}',
				'rules' => [
					['code' => 250, 'message' => 'Sender invalid']
				]
			]);
			$this->command('recipient',[
				'definition' => 'RCPT TO:<{destination}>',
				'rules' => [[
					'code' => [250,220,251],
					'message' => "Ошибка, адрес не может быть доступен",
					'negated' => true
				]]
			]);
			$this->bundle('data',[[
				'definition' => 'DATA',
				'rules' => [
					['code' => 354, 'message' => 'DATA pre send error']
				]
			],[
				'definition' => "{data}\r\n.",
				'rules' => [
					'code' => [220,250], 'message' => 'SMTP server not accepted send data'
				]
			]]);
			$this->command('reset', [ 'definition' => 'RESET' ]);
			$this->command('quit', [ 'definition' => 'QUIT' ]);
		}


		/**
		 * @return mixed
		 */
		public function getMaxLength(){
			return 515;
		}

		/**
		 * @param $response
		 * @return int
		 */
		public function recognizeCode($response){
			return intval(substr($response,0,3));
		}

		/**
		 * @param $command
		 * @return string
		 */
		public function convertBeforeSend($command){
			return $command . "\r\n";
		}


		/**
		 * @param ConnectionInteractionInterface $connection
		 * @return string
		 */
		public function read(ConnectionInteractionInterface $connection){
			$data = "";$length = $this->getMaxLength();
			while($str = $connection->read($length)){
				$data .= $str;
				if(substr($str,3,1) == " ") { break; }
			}
			return $data;
		}

		/**
		 * @return ConnectionInterface
		 */
		public function createConnection(){
			return new Socket([]);
		}

		/**
		 * @param ProcessSequenceInterface $processSequence
		 * @return mixed
		 */
		public function beforeSequence(ProcessSequenceInterface $processSequence){
			$this->getCommand('start')->run($processSequence,[]);
		}

		/**
		 * @param $processSequence
		 * @return mixed
		 */
		public function afterSequence(ProcessSequenceInterface $processSequence){}

		/**
		 * @param ProcessSequenceInterface $processSequence
		 * @return mixed
		 */
		public function continueSequence(ProcessSequenceInterface $processSequence){

		}
	}
}

