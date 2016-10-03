<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 23:40
 */
namespace Jungle\Messenger\Mail\SMTP {

	use Jungle\Messenger;
	use Jungle\Messenger\ICombination;
	use Jungle\Messenger\IContact;
	use Jungle\Messenger\Mail\Contact;
	use Jungle\Messenger\Mail\IMessage;
	use Jungle\User\AccessAuth\Auth;
	use Jungle\Util\Communication\SequenceInterface;
	use Jungle\Util\Communication\Stream;
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Specifications\TextTransfer\Body\Multipart;
	use Jungle\Util\Specifications\TextTransfer\Document;

	/**
	 * Class SMTP
	 * @package Jungle\Messenger\Mail\SMTP
	 */
	class SMTP extends Messenger{

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

				'host'              => null,
				'port'              => null,
				'scheme'            => null,
				'timeout'           => null,

				'auth'              => null,


				'from'              => null,

				'charset'           => 'utf-8',
				'change_headers'    => null,
				'extra_headers'     => null,

				'timezone'          => 3,

				'interval'          => 10,
				'max_destinations'  => 30,
				'mailer_service'    => 'PHP Jungle.messager.SMTP',
			],$options));
			$this->options['auth']      = Auth::getAccessAuth($this->options['auth']);
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
			if($destination instanceof Messenger\Mail\IContact){
				switch($destination->getType()){
					case Messenger\Mail\IContact::TYPE_MAIN:
						if(count($this->to) < 1){
							$this->to[] = $destination;
						}else{
							$this->cc[] = $destination;
						}
						break;
					case Messenger\Mail\IContact::TYPE_CC:
						$this->cc[] = $destination;
						break;
					case Messenger\Mail\IContact::TYPE_BCC:
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
			 * @var IMessage $messageObject
			 * @var IContact[] $destinations
			 */

			$messageObject  = $this->combination->getMessage();

			$host           = $this->options['host'];
			$port           = $this->options['port'];
			$scheme         = $this->options['scheme'];


			$auth           = $this->options['auth'];

			$from           = $this->options['from'];
			$author         = $messageObject->getAuthor()?:$from;

			$destinations   = [];


			$document = new Document();
			$document->setHeader('Date',            date("D, j M Y G:i:s")." +0{$this->options['timezone']}00");
			$document->setHeader('From',            "{$this->prepareString($from->getName())} <{$from->getAddress()}>");
			$document->setHeader('Sender',          "{$this->prepareString($author->getName())} <{$author->getAddress()}>");
			$document->setHeader('X-Mailer',        $this->options['mailer_service']);
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
				$to[] = ($d instanceof Messenger\Mail\IContact && $d->getName()?$this->prepareString($d->getName()):'')." <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($to) $document->setHeader('To',implode('; ',$to));

			$cc = [];
			foreach($this->cc as $d){
				$cc[] = ($d instanceof Messenger\Mail\IContact && $d->getName()?$this->prepareString($d->getName()):'')." <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($cc) $document->setHeader('Cc',implode('; ',$cc));

			$bcc = [];
			foreach($this->bcc as $d){
				$bcc[] = ($d instanceof Messenger\Mail\IContact && $d->getName()?$this->prepareString($d->getName()):'')." <{$d->getAddress()}>";
				$destinations[] = $d;
			}
			if($bcc) $document->setHeader('Bcc',implode('; ',$bcc));
			if($messageObject->hasAttachments()){

				$body = new Multipart();

				$main = new Document();
				$main->setHeader('Content-Type',"{$messageObject->getType()}; charset={$this->options['charset']}");
				$main->setHeader('Content-Transfer-Encoding',"8bit");
				$main->setBody($messageObject->getContent());

				$body->addPart($main);

				foreach($messageObject->getAttachments() as $attachment){
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
				$t = $messageObject->getType();
				if(!$t){
					$t = 'text/plain';
				}
				$document->setHeader('Content-Type',"{$t}; charset={$this->options['charset']}");
				$document->setHeader('Content-Transfer-Encoding',"8bit");
				$document->setBody($messageObject->getContent());
			}
			$message = $document->represent();


			$sequence = $this->getSequence();
			$sequence->run([
				'login'     => $auth->getBase64Login(),
				'password'  => $auth->getBase64Password(),

				'mail_from' => $from->getAddress(),
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
					'scheme'     => $this->options['scheme'],
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

