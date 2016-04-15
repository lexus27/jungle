<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 23:40
 */
namespace Jungle\Messager\Mail\SMTP {

	use Jungle\Communication\Stream;
	use Jungle\Communication\Stream\Connection;
	use Jungle\Communication\URL;
	use Jungle\Messager;
	use Jungle\Messager\ICombination;
	use Jungle\Messager\IContact;
	use Jungle\Messager\Mail\Contact;
	use Jungle\Messager\Mail\IMessage;
	use Jungle\Specifications\TextTransfer\Body\Multipart;
	use Jungle\Specifications\TextTransfer\Document;
	use Jungle\User\AccessAuth\Auth;

	/**
	 * Class SMTP
	 * @package Jungle\Messager\Mail\SMTP
	 */
	class SMTP extends Messager{

		/** @var int */
		protected $added_destinations = 0;

		/** @var int */
		protected $flushed = 0;

		/** @var ICombination */
		protected $combination;

		/** @Total for one send collection count $this->options['max_destinations'] */

		/** @var IContact[] */
		protected $to = [];

		/** @var IContact[] */
		protected $cc = [];

		/** @var IContact[]*/
		protected $bcc = [];


		/** @var  IContact[] */
		protected $_current_destinations = [];

		/** @var array  */
		protected $_commands = [];


		/**
		 * @param array $options
		 */
		public function __construct(array $options = []){
			parent::__construct(array_merge([
				'charset'           => 'utf-8',
				'url'               => null,
				'timeout'           => 5,
				'auth'              => null,
				'from'              => null,
				'change_headers'    => null,
				'extra_headers'     => null,

				'timezone'          => 3,

				'interval'          => 10,
				'max_destinations'  => 30,
				'mailer_service'    => 'PHP Jungle.messager.SMTP',
			],$options));
			$this->options['auth']      = Auth::getAccessAuth($this->options['auth']);
			$this->options['url']       = URL::getURL($this->options['url']);
			$this->options['from']      = Contact::getContact($this->options['from']);
		}

		/**
		 * @param ICombination $combination
		 * @return void
		 */
		protected function begin(ICombination $combination){
			$this->combination = $combination;
			$this->to = [];
			$this->cc = [];
			$this->bcc = [];
		}

		/**
		 * @param IContact $destination
		 * @return void
		 */
		protected function registerDestination(IContact $destination){
			if($destination instanceof Messager\Mail\IContact){
				switch($destination->getType()){
					case Messager\Mail\IContact::TYPE_MAIN:
						if(count($this->to) < 1){
							$this->to[] = $destination;
						}else{
							$this->cc[] = $destination;
						}
						break;
					case Messager\Mail\IContact::TYPE_CC:
						$this->cc[] = $destination;
						break;
					case Messager\Mail\IContact::TYPE_BCC:
						$this->bcc[] = $destination;
						break;
				}
			}else{
				$this->to[] = $destination;
			}

			$this->added_destinations++;

			if($this->added_destinations >= $this->options['max_destinations']){
				$this->flushSend();
			}
		}

		/**
		 * @param ICombination $combination
		 * @return void
		 */
		protected function complete(ICombination $combination){
			$this->flushSend();
		}

		/**
		 *
		 */
		protected function flushSend(){
			if($this->added_destinations){
				if($this->flushed > 0){
					sleep($this->options['interval']);
				}
				$this->sendCollection();
				$this->to = [];
				$this->cc = [];
				$this->bcc = [];
				$this->added_destinations = 0;
				$this->flushed++;
			}
		}

		/**
		 * @param $string
		 * @return string
		 */
		protected function prepareString($string){
			return "=?{$this->options['charset']}?Q?".str_replace("+","_",str_replace("%","=",urlencode($string)))."?=";
		}

		/**
		 *
		 */
		protected function sendCollection(){
			if(!$this->to) throw new \LogicException();
			/**
			 * @var Contact $from
			 * @var Contact $author
			 * @var \Jungle\User\AccessAuth\Pair $auth
			 * @var URL $url
			 * @var IMessage $m
			 * @var IContact[] $destinations
			 */
			$from           = $this->options['from'];
			$author         = $m->getAuthor()?:$from;
			$url            = $this->options['url'];
			$m              = $this->combination->getMessage();
			$destinations   = [];


			$document = new Document();
			$document->setHeader('Date',            date("D, j M Y G:i:s")." +0{$this->options['timezone']}00");
			$document->setHeader('From',            "{$this->prepareString($from->getName())} <{$from->getAddress()}>");
			$document->setHeader('Sender',          "{$this->prepareString($author->getName())} <{$author->getAddress()}>");
			$document->setHeader('X-Mailer',        $this->options['mailer_service']);
			$document->setHeader('Reply-To',        $document->getHeader('From'));
			$document->setHeader('X-Priority',      "3 (Normal)");
			$document->setHeader('Message-ID',      "<172562218.".date("YmjHis")."@{$url->getHost()}>");
			$document->setHeader('Subject',         "{$this->prepareString($m->getSubject())}");
			$document->setHeader('MIME-Version',    "1.0");


			if(is_array($this->options['change_headers'])){
				$document->setHeaders($this->options['change_headers'],true);
			}

			if(is_array($this->options['extra_headers'])){
				$document->setHeaders($this->options['extra_headers'],false);
			}

			$to = [];
			foreach($this->to as $d){
				$to[] = ($d instanceof Messager\Mail\IContact && $d->getName()?$this->prepareString($d->getName()):'')." <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($to) $document->setHeader('To',implode('; ',$to));

			$cc = [];
			foreach($this->cc as $d){
				$cc[] = ($d instanceof Messager\Mail\IContact && $d->getName()?$this->prepareString($d->getName()):'')." <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($cc) $document->setHeader('Cc',implode('; ',$cc));

			$bcc = [];
			foreach($this->bcc as $d){
				$bcc[] = ($d instanceof Messager\Mail\IContact && $d->getName()?$this->prepareString($d->getName()):'')." <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($bcc) $document->setHeader('Bcc',implode('; ',$bcc));
			if($m->hasAttachments()){

				$body = new Multipart();

				$main = new Document();
				$main->setHeader('Content-Type',"{$m->getType()}; charset={$this->options['charset']}");
				$main->setHeader('Content-Transfer-Encoding',"8bit");
				$main->setBody($m->getContent());

				$body->addPart($main);

				foreach($m->getAttachments() as $attachment){
					$a = new Document();
					$a->setHeader('Content-Type',"{$attachment->getType()}; name=\"{$attachment->getName()}\"");
					$a->setHeader('Content-Transfer-Encoding',"base64");
					$a->setHeader('Content-Disposition',"{$attachment->getDisposition()}; filename=\"{$attachment->getName()}\"");
					$a->setHeaders($attachment->getHeaders(),false);
					$a->setBody($attachment->getRaw());

					$body->addPart($a);
				}
				$document->setBody($body);
			}else{
				$document->setHeader('Content-Type',"{$m->getType()}; charset={$this->options['charset']}");
				$document->setHeader('Content-Transfer-Encoding',"8bit");
				$document->setBody($m->getContent());
			}
			$message = $document->represent();
			$stream = $this->getStream();
			$stream->reset()->execute([
				'defaults' => [
					'rule' => [
						'negated' => true
					]
				],
				'commands' => [
					[
						'EHLO '.$url->getHost(),[
						'code' => 250, 'msg' => 'SMTP Hello error'
					]
					],[
						'AUTH LOGIN',[
							'code' => 334, 'msg' => 'Authentication error(START)'
						]
					],[
						$auth->getBase64Login(),[
							'code' => 334, 'msg' => 'Authentication error(login)'
						]
					],[
						$auth->getBase64Password(),[
							'code'=> 235, 'msg' => 'Authentication error(password)'
						]
					],[
						"MAIL FROM:<{$from->getAddress()}> SIZE=".strlen($message),[
							'code' => 250, 'msg' => 'Sender invalid'
						]
					],[
						'collection' => $destinations,
						'handler'    => function($index,IContact $destination){
							return [
								"RCPT TO:<{$destination->getAddress()}>",[
									'code' => [250,220,251],
									'msg' => "Ошибка, адрес {$destination->getAddress()} не может быть доступен",
									'negated' => true
								]
							];
						}
					], [
						'DATA',[
						'code' => 354, 'msg' => 'DATA pre send error'
						]
					],[
						"$message\r\n.",[
							'code' => [220,250], 'msg' => 'SMTP server not accepted send data'
						]
					], 'QUIT'
				]
			])->reset();
		}

		/**
		 * @return Stream\Specification
		 */
		protected function getSpecification(){
			static $s;
			if(!$s){
				$s = new Stream\Specification();
				$s->setCodeRecognizer(function($d){return intval(substr($d,0,3));});
				$s->setCommandStructureModifier(function($d){return $d . "\r\n";});
				$s->setReader(function($fp){
					$data = "";
					while($str = fgets($fp,515)){
						$data .= $str;
						if(substr($str,3,1) == " ") { break; }
					}
					return $data;
				});

			}
			return $s;
		}

		/**
		 * @return Stream
		 */
		protected function getStream(){
			static $s;
			if(!$s){
				$s = $this->getSpecification()->openStream([
					'url'       => $this->options['url'],
					'timeout'   => isset($this->options['timeout'])?$this->options['timeout']:3,
					'start'     => [
						null, [
							'code' => 220, 'msg' => 'Error connect to SMTP server', 'negated' => true
						]
					]
				]);
			}
			return $s;
		}

	}
}

