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
				'rules' => [
					[ 'check' => [220], 'message' => 'Error connect to SMTP server', 'negate' => true ]
				]
			]);
			$this->command('hello',[
				'definition' => 'EHLO {{host}}',
				'rules' => [
					[ 'check' => 250, 'message' => 'SMTP Hello error', 'negate' => true ]
				]
			]);
			$this->bundle('auth',[[
				'definition' => 'AUTH LOGIN',
				'rules' => [
					['check' => 334, 'message' => 'Authentication error(START)', 'negate' => true]
				]
			],[
				'definition' => '{{login}}',
				'rules' => [
					['check' => 334, 'message' => 'Authentication error(login)', 'negate' => true]
				]
			],[
				'definition' => '{{password}}',
				'rules' => [
					['check'=> 235, 'message' => 'AuthenticationMissed', 'negate' => true]
				]
			]]);
			$this->command('mail_from',[
				'definition' => 'MAIL FROM:<{{mail_from}}> SIZE={{size}}',
				'rules' => [
					['check' => 250, 'message' => 'Sender invalid','negate' => true]
				]
			]);
			$this->command('recipient',[
				'definition' => 'RCPT TO:<{{recipient}}>',
				'aggregator' => function(array $params){
					$a = [];
					if(isset($params['recipient'])){
						if(is_scalar($params['recipient'])){
							$a[] = $params['recipient'];
						}
						if(is_callable($params['recipient'])){
							$params['recipient'] = call_user_func($params['recipient']);
						}
						foreach($params['recipient'] as $recipient){
							$a[] = [
								'recipient' => $recipient
							];
						}
					}
					return $a;
				},
				'rules' => [[
					'check'    => [250,220,251],
					'message' => "Ошибка, адрес не может быть доступен",
					'negate'  => true
				]]
			]);
			$this->bundle('data',[[
				'definition' => 'DATA',
				'rules' => [
					['check' => 354, 'message' => 'DATA pre send error','negate' => true]
				]
			],[
				'definition' => "{{data}}\r\n.",
				'rules' => [
					['check' => [220,250], 'message' => 'SMTP server not accepted send data','negate' => true]
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
			while($str = $connection->readLine($length)){
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

