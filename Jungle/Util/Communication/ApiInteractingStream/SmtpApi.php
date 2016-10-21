<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.10.2016
 * Time: 22:26
 */
namespace Jungle\Util\Communication\ApiInteractingStream {

	use Jungle\Util\Communication\ApiInteracting\Combination;
	use Jungle\Util\Communication\Stream\StreamInteractionInterface;

	/**
	 * Class SmtpApi
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	class SmtpApi extends Api{

		/** @var int  */
		protected $max_length = 512;


		/**
		 * SmtpApi constructor.
		 */
		public function __construct(){

			$this->setAction('start', new Action($this, '',
				function(Process $process){
					if($process->getCode() !== 220){
						throw new \Exception('Error connect to SMTP server: '.$process->getResult());
					}
				}
			));

			$this->setAction('hello', new Action($this, 'EHLO {{host}}',
				function(Process $process){
					if($process->getCode() !== 250){
						throw new \Exception('SMTP Hello error: '.$process->getResult());
					}
				}
			));

			$this->setAction('auth', new ActionComposite($this,
				new Action($this, 'AUTH LOGIN', function(Process $process){
					if($process->getCode() !== 334)
						throw new \Exception('SMTP error pre auth: '.$process->getResult());
				}),
				new Action($this, '{{login}}', function(Process $process){
					if($process->getCode() !== 334)
						throw new \Exception('SMTP error pass auth: '.$process->getResult());
				}),
				new Action($this, '{{password}}', function(Process $process){
					if($process->getCode() !== 235)
						throw new \Exception('SMTP AuthenticationError: '.$process->getResult());
				})
			));

			$this->setAction('mail_from', new Action($this, 'MAIL FROM:<{{mail_from}}> SIZE={{size}}',
				function(Process $process){
					if($process->getCode() !== 250){
						throw new \Exception('SMTP sender error: '.$process->getResult());
					}
				}
			));


			$this->setAction('recipient', new ActionRepeater($this, 'RCPT TO:<{{recipient}}>',
				function(array $params){
					$a = [];
					if(isset($params['recipient'])){
						if(is_scalar($params['recipient'])){
							$a[] = [ 'recipient' => $params['recipient']];
						}
						if(is_callable($params['recipient'])){
							$params['recipient'] = call_user_func($params['recipient']);
						}
						if(is_array($params['recipient'])){
							foreach($params['recipient'] as $recipient){
								$a[] = [ 'recipient' => $recipient ];
							}
						}

					}
					return $a;
				},
				function(Process $process){
					if(!in_array($process->getCode(),[250,220,251], true)){
						throw new \Exception('SMTP recipient error: '.$process->getResult());
					}
				}
			));
			$this->setAction('data', new ActionComposite($this,
				new Action($this, 'DATA', function(Process $process){
					if($process->getCode() !== 354)
						throw new \Exception('SMTP data prepare error: '.$process->getResult());
				}),
				new Action($this, "{{data}}\r\n.", function(Process $process){
					if(!in_array($process->getCode(),[220,250], true))
						throw new \Exception('SMTP data not accepted: '.$process->getResult());
				})
			));
			$this->setAction('reset', new Action($this, 'RESET'));
			$this->setAction('quit',new Action($this, 'QUIT'));
		}

		/**
		 * @param Combination $combination
		 */
		public function before(Combination $combination){
			parent::before($combination);
			$this->executeAction($this->getAction('start'), $combination);
		}


		/**
		 * @param StreamInteractionInterface $stream
		 * @return string
		 */
		public function read(StreamInteractionInterface $stream){
			$data = '';$length = $this->max_length;
			while($line = $stream->readLine($length)){
				$data .= $line;
				if(substr($line,3,1) == " ")break;
			}
			return $data;
		}


		/**
		 * @param $command
		 * @return string
		 */
		protected function packCommand($command){
			return $command . "\r\n";
		}


		/**
		 * @param $answer
		 * @return string
		 */
		public function code($answer){
			return intval(substr($answer,0,3));
		}

		/**
		 * @param Process $process
		 * @throws \Exception
		 */
		public function validateProcess(Process $process){
			if($process->getCode() >= 500){
				throw new \Exception('SMTP FatalError: '.$process->getResult());
			}
		}

	}
}

