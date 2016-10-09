<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 23:40
 */
namespace Jungle\Messenger\Mail\SMTP {

	use Jungle\Messenger;
	use Jungle\Messenger\CombinationInterface;
	use Jungle\Messenger\ContactInterface;
	use Jungle\Messenger\Mail\Contact;
	use Jungle\Messenger\Mail\MessageInterface;
	use Jungle\User\AccessAuth\Auth;
	use Jungle\Util\Communication\SequenceInterface;
	use Jungle\Util\Specifications\Hypertext\Content\Multipart;
	use Jungle\Util\Specifications\Hypertext\Document;

	/**
	 * Class SMTP
	 * @package Jungle\Messenger\Mail\SMTP
	 */
	class SMTP extends Messenger{

		/** @var int */
		protected $added_destinations = 0;

		/** @var int */
		protected $flushed = 0;

		/** @var CombinationInterface */
		protected $combination;

		/** @Total for one send collection count $this->options['max_destinations'] */

		/** @var ContactInterface[] */
		protected $to = [];

		/** @var ContactInterface[] */
		protected $cc = [];

		/** @var ContactInterface[]*/
		protected $bcc = [];


		/** @var  ContactInterface[] */
		protected $_current_destinations = [];

		/** @var array  */
		protected $_commands = [];


		/**
		 * @param array $options
		 */
		public function __construct(array $options = []){
			$this->options = array_merge([

				'host'              => null,
				'port'              => null,
				'transport'         => null,
				'timeout'           => null,

				'auth'              => null,

				'sender_from_login'     => true,
				'sender'              => null,

				'charset'           => ini_get('default_charset'),
				'change_headers'    => null,
				'extra_headers'     => null,

				'timezone'          => 3,

				'interval'          => 10,
				'max_destinations'  => 30,
				'agent'             => 'JungleFramework Messenger',
			],$options);
			$this->options['auth'] = $auth = Auth::getAccessAuth($this->options['auth']);
			if(!$this->options['sender'] && $this->options['sender_from_login']){
				$this->options['sender'] = $auth->getLogin();
			}
			$this->options['sender']      = Contact::getContact($this->options['sender']);
		}

		/**
		 * @param CombinationInterface $combination
		 * @return void
		 */
		protected function begin(CombinationInterface $combination){
			$this->combination = $combination;
			$this->to = [];
			$this->cc = [];
			$this->bcc = [];
		}

		/**
		 * @param ContactInterface $destination
		 * @return void
		 */
		protected function registerDestination(ContactInterface $destination){
			if($destination instanceof Messenger\Mail\ContactInterface){
				switch($destination->getType()){
					case Messenger\Mail\ContactInterface::TYPE_MAIN:
						if(count($this->to) < 1){
							$this->to[] = $destination;
						}else{
							$this->cc[] = $destination;
						}
						break;
					case Messenger\Mail\ContactInterface::TYPE_CC:
						$this->cc[] = $destination;
						break;
					case Messenger\Mail\ContactInterface::TYPE_BCC:
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
		 * @param CombinationInterface $combination
		 * @return void
		 */
		protected function complete(CombinationInterface $combination){
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
			 * @var Contact $sender
			 * @var Contact $author
			 * @var \Jungle\User\AccessAuth\Pair $auth
			 * @var MessageInterface $messageObject
			 * @var ContactInterface[] $destinations
			 */

			$messageObject  = $this->combination->getMessage();

			$host           = $this->options['host'];
			$port           = $this->options['port'];
			$transport      = $this->options['transport'];
			$sender           = $this->options['sender'];


			$auth           = $this->options['auth'];

			$author         = $messageObject->getAuthor()?:$sender;

			$destinations   = [];


			$document = new Document();
			$document->setHeader('Date',            date("D, j M Y G:i:s")." +0{$this->options['timezone']}00");
			$document->setHeader('From',            "{$this->prepareString($sender->getName())} <{$sender->getAddress()}>");
			$document->setHeader('Sender',          "{$this->prepareString($author->getName())} <{$author->getAddress()}>");
			$document->setHeader('X-Mailer',        $this->options['agent']);
			$document->setHeader('Reply-To',        $document->getHeader('From'));
			$document->setHeader('X-Priority',      "3 (Normal)");
			$document->setHeader('Message-ID',      "<172562218.".date("YmjHis")."@{$host}>");
			$document->setHeader('Subject',         "{$this->prepareString($messageObject->getSubject())}");
			$document->setHeader('MIME-Version',    "1.0");


			if(is_array($this->options['change_headers'])){
				$document->setHeaders($this->options['change_headers'],true);
			}

			if(is_array($this->options['extra_headers'])){
				$document->setHeaders($this->options['extra_headers'],false);
			}

			$to = [];
			foreach($this->to as $d){
				$to[] = ($d instanceof Messenger\Mail\ContactInterface && $d->getName()?$this->prepareString($d->getName()):'') . " <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($to) $document->setHeader('To',implode('; ',$to));

			$cc = [];
			foreach($this->cc as $d){
				$cc[] = ($d instanceof Messenger\Mail\ContactInterface && $d->getName()?$this->prepareString($d->getName()):'') . " <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($cc) $document->setHeader('Cc',implode('; ',$cc));

			$bcc = [];
			foreach($this->bcc as $d){
				$bcc[] = ($d instanceof Messenger\Mail\ContactInterface && $d->getName()?$this->prepareString($d->getName()):'') . " <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($bcc) $document->setHeader('Bcc',implode('; ',$bcc));
			if($messageObject->hasAttachments()){

				$body = new Multipart();

				$main = new Document();
				$main->setHeader('Content-Type',"{$messageObject->getType()}; charset={$this->options['charset']}");
				$main->setHeader('Content-Transfer-Encoding',"8bit");
				$main->setContent($messageObject->getContent());

				$body->addPart($main);

				foreach($messageObject->getAttachments() as $attachment){
					$a = new Document();
					$a->setHeader('Content-Type',"{$attachment->getType()}; name=\"{$attachment->getName()}\"");
					$a->setHeader('Content-Transfer-Encoding',"base64");
					$a->setHeader('Content-Disposition',"{$attachment->getDisposition()}; filename=\"{$attachment->getName()}\"");
					$a->setHeaders($attachment->getHeaders(),false);
					$a->setContent($attachment->getRaw());

					$body->addPart($a);
				}
				$document->setContent($body);
			}else{
				$t = $messageObject->getType();
				if(!$t){
					$t = 'text/plain';
				}
				$document->setHeader('Content-Type',"{$t}; charset={$this->options['charset']}");
				$document->setHeader('Content-Transfer-Encoding',"8bit");
				$document->setContent($messageObject->getContent());
			}
			$message = (string)$document;


			$sequence = $this->getSequence();
			$sequence->run([
				'login'     => $auth->getBase64Login(),
				'password'  => $auth->getBase64Password(),

				'mail_from' => $sender->getAddress(),
				'recipient' => function() use($destinations) {
					$a = [];
					foreach($destinations as $contact){
						$a[] = $contact->getAddress();
					};
					return $a;
				},
				'data' => $message,
				'size' => strlen($message)
			], true);
		}

		/**
		 * @return SequenceInterface
		 */
		protected function getSequence(){
			static $sequence;
			if(!$sequence){
				$specification = new \Jungle\Util\Communication\Sequence\Specification\Smtp();
				$sequence = $specification->createSequence();
				/** @var Auth $auth */
				$auth = $this->options['auth'];
				$sequence->setConfig([
					'params_merge'  => true,

					'host'       => $this->options['host'],
					'port'       => $this->options['port'],
					'transport'  => $this->options['transport'],
					'timeout'    => isset($this->options['timeout'])?$this->options['timeout']:null,

					'login'      => $auth->getBase64Login(),
					'password'      => $auth->getBase64Login(),
				]);

				$sequence->setSequence([
					'hello',
					'auth',
					'mail_from',
					'recipient',
					'data'
				]);
			}
			return $sequence;
		}

	}
}

