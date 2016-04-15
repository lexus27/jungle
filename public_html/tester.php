<?php

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
	'Jungle' => dirname(__DIR__) .  '/core/Jungle/'
]);
$loader->register();
echo '<pre>';

var_dump(\Jungle\TypeHint::getInstance()->check('[s]',['assoc' => true,0=>false],null,false));





/*
$s = '[Object.type([User.param])] = [User.group.type]';
preg_match_all('@(\[ (?: (?: (?>[^\[\]]+) | (?R) )*) \]|[\w\\\\\s/]+)?@sxmi',$s,$m);
var_dump($m);

$s = 'Object.getName([User.name]).type([Object.go], 1, [Select]) = [User.group.type]';

$allRegex = '@
(\[(?:(?>[\[\]]+) | (?R))\]) |
(\w+[\.\w\d]+\(.*?\)) |
\s*? ([\+\-\*\/\$\#\@\%\^\&\=]+) \s*? |
(\w+[\.\w\d]+)
@sxmi';
$functionRegex = '@(\w+[\.\w\d]+)\(([^)]+)\)$@';
preg_match_all($allRegex,$s,$m);
var_dump($m);
/*
$data = [
	'left' => null,
	'operator' => null,
	'right' => null
];
foreach($m[0] as $i => $string){
	$string = trim(trim($string),'[]');

	if($m[3][$i]){
		$data[] = $string; // operator
	}elseif($m[2][$i]){

		preg_match()

	}else{

		$data[] = $string;

		if($m[2][$i]){
			$left = parseFunctionCall($string);
		}else{
			$left = $string;
		}

	}


}

/*
preg_match_all(,$s,$m);
var_dump($m);

$left = null;
$operator = null;
$right = null;

function parseFunctionCall($string){

	$string = preg_replace_callback('@(\w+[\.\w\d]+)\(([^)]+)\)$@',function($matches){
		echo 11;
		var_dump($matches);
		return $matches[1] . '('.'HELOO'.')';

	},$string);
	var_dump($string);
	return $string;
}

function parseIdentifier($string){

	$string = trim(trim($string),'[]');

}
function getValue($path){
	static $i = 0;
	return '{VALUE-'.++$i.'}';
}


foreach($m[0] as $i => $string){
	$string = trim(trim($string),'[]');

	if(!$left){

	}

	if($m[1][$i]){
		preg_match();
	}

	if($m[3][$i]){
		$operator = $string;
	}elseif(!$left){

		if($m[2][$i]){
			$left = parseFunctionCall($string);
		}else{
			$left = $string;
		}

	}


}
*/
/*

$specification = new \Jungle\Communication\Stream\Specification();
$specification->setCode(100,'Info');
$specification->setCode(200,'Success');
$specification->setCode(300,'Notify');
$specification->setCode(400,'Warning');
$specification->setCode(500,'Failure');
$specification->setCommandStructureModifier(function($s){
	return $s . ':SSS'."\r\n";
});
$specification->setCodeRecognizer(function($r){return intval(substr($r,0,3));});
$stream = new \Jungle\Communication\Stream(new \Jungle\Communication\Stream\Connection\Test());
$stream->setSpecification($specification);
try{
	$defaults = [
		'rule' => [
			'negated'   => true,
			'msg'       => 'Error'
		]
	];
	$stream->execute([
		'defaults' => $defaults,
		'commands' => [
			[
				'command' => 'EHLO',
				'rules' => [
					'code' => [200,300,400],
					'msg' => 'Ошибка команды EHLO'
				]
			], [
				'command' => 'APAVLO',
				'rules' => [[
					'code' => 300,
					'msg' => 'Ошибка команды APAVLO код ответа не является 300',
					'behaviour' => function(\Jungle\Communication\Stream\Specification $spec,\Jungle\Communication\Stream\Command $command, $matched) use($stream, $defaults){
						if(!$matched){

							$stream->execute([
								'defaults' => $defaults,
								'commands' => [
									[
										'command' => 'Password 12231',
										'rules' => 201,
										'msg' => 'LOL'
									]
								]

							]);

						}
					}
				]]
			]

		]
	]);
}catch(\Jungle\Communication\Stream\Exception $e){

}


print_r($stream);
*/

/*



$domain = \Jungle\Communication\URL\Host\Domain::get('google.ru');


$url = \Jungle\Communication\URL::getURL('http://google.com/');

$url->getHostIP();
print_r($url);
echo $url;*/



/*
$c = new Jungle\Communication\Stream\Connection\Socket();
$c->setUrl(\Jungle\Communication\URL::getURL('ssl://smtp.mail.ru:465'));
$c->setTimeout(10);
$fp = $c->getResource();
$r = function($fp){
	$data="";
	while($str = fgets($fp,515)){
		$data .= $str;
		if(substr($str,3,1) == " ") { break; }
	}
	return $data;
};


echo nl2br($r($fp)) . '</br>';
fwrite($fp,'EHLO mail'.PHP_EOL);
echo nl2br($r($fp)) . '</br>';
echo nl2br($c->read(512,$r)) . '</br>';
flush();
echo '</br>',$c->send('EHLO mail'.PHP_EOL,11),'</br>';
flush();
echo nl2br($c->read(512,$r)) . '</br>';*/
/*




use Jungle\Messager;
use Jungle\Messager\Mail\Contact;

$messager = new Messager\Mail\SMTP\SMTP([
	'auth'      => ['lexus.1995@mail.ru', '@JungleTechLabs2016@'],
	'mailer'    => 'Алексей Кутузов <lexus.1995@mail.ru>',
	'url'       => 'ssl://smtp.mail.ru:465',
]);

$attachment = new Messager\Mail\Attachment();
$attachment->setRaw('<div style="color:red">Это вложение!.</div>');
$attachment->setName('text.html');

$message = new Messager\Mail\Message();
$message->setSubject('');
$message->setContent('<a href="cid:text.html">Посмотрите это письмо</a>');
$message->setType('text/html');

$message->addAttachment($attachment);

$contact1 = Contact::getContact('Алексей <lexus.1995@mail.ru>');
$contact2 = Contact::getContact('Алексей CC <lexus27.khv@gmail.com>')->setType(Contact::TYPE_CC);
$contact3 = Contact::getContact('Алексей BCC <lexus27.khv@gmail.com>')->setType(Contact::TYPE_BCC);
$combo = new Messager\Combination();
$combo->addDestination($contact1);
$combo->addDestination($contact2);
$combo->addDestination($contact3);
$combo->setMessage($message);
$messager->send($combo);


*/

/*


use Jungle\Specifications\TextTransfer\Body\Multipart;
use Jungle\Specifications\TextTransfer\Document;

$document = new Document();
$multipart = new Multipart();

$body = new Document();
$body->setHeader('Content-Type','text/html');
$body->setHeader('Content-Transfer-Encoding','8bit');
$body->setBody('Привет земляк!');
$multipart->addPart($body);


$a1 = new Document();
$a1->setHeader('Content-Type','application/octet-stream');
$a1->setHeader('Content-Transfer-Encoding','base64');
$a1->setBody('Привет земляк!');
$multipart->addPart($a1);


$partHolder = new Document();
$partHolder->setHeader('Content-Type','application/octet-stream');
$multipart1 = new Multipart();
$part1 = new Document();
$part1->setHeader('Content-Type','text/plain; name="simple.txt"');
$part1->setHeader('Content-Transfer-Encoding','8bit');
$part1->setHeader('Content-Disposition','attachment; filename="simple.txt" ');
$part1->setBody('Привет');
$multipart1->addPart($part1);
$partHolder->setBody($multipart1);


$multipart->addPart($partHolder);

$document->setBody($multipart);

$multipart->addPart($body);

echo nl2br(htmlentities($document));*/
/*

$partitions = '
Delivered-To: lexus27.khv@gmail.com
Received: by 10.27.129.215 with SMTP id c206csp289644wld;
        Thu, 7 Jan 2016 22:59:48 -0800 (PST)
X-Received: by 10.112.11.234 with SMTP id t10mr39852803lbb.91.1452236388471;
        Thu, 07 Jan 2016 22:59:48 -0800 (PST)
Return-Path: <lexus.1995@mail.ru>
Received: from smtp12.mail.ru (smtp12.mail.ru. [94.100.181.93])
        by mx.google.com with ESMTPS id d15si37204101lfd.95.2016.01.07.22.59.48
        for <lexus27.khv@gmail.com>
        (version=TLS1 cipher=AES128-SHA bits=128/128);
        Thu, 07 Jan 2016 22:59:48 -0800 (PST)
Received-SPF: pass (google.com: domain of lexus.1995@mail.ru designates 94.100.181.93 as permitted sender) client-ip=94.100.181.93;
Authentication-Results: mx.google.com;
       spf=pass (google.com: domain of lexus.1995@mail.ru designates 94.100.181.93 as permitted sender) smtp.mailfrom=lexus.1995@mail.ru;
       dkim=pass header.i=@mail.ru;
       dmarc=pass (p=NONE dis=NONE) header.from=mail.ru
DKIM-Signature: v=1; a=rsa-sha256; q=dns/txt; c=relaxed/relaxed; d=mail.ru; s=mail2;
	h=Content-Type:Mime-Version:Subject:Cc:To:Message-Id:Reply-To:From:Date; bh=R3y6Ex2wiZi2HlCmu4ATLd3ZDU8EPSP6dzSCEqEq1xc=;
	b=apPlBD5u28FtGQg/1W1dSdb2HyZvC512uJiW8FoR7V6iM39BMlnpKZORIiXL0yGOt6wunpr3hqlH19TgqJnEv890dGAjUkozJYs00/GsZ1aXI5GqT1UzwjXfQ5/U6vPMVwODxJqUd/bArqohvg2velTSIdrww6WuRB0Qqw/Of0c=;
Received: from [95.70.15.55] (port=2102 helo=btko)
	by smtp12.mail.ru with esmtpa (envelope-from <lexus.1995@mail.ru>)
	id 1aHR1X-0008OR-GF; Fri, 08 Jan 2016 09:59:47 +0300
Date: Fri, 8 Jan 2016 9:59:29 +0700
From: =?utf-8?Q?=D0=90=D0=BB=D0=B5=D0=BA=D1=81=D0=B5=D0=B9_=D0=9A=D1=83=D1=82=D1=83=D0=B7=D0=BE=D0=B2?= <lexus.1995@mail.ru>
X-Mailer: PHP Jungle.messager.SMTP
Reply-To: =?utf-8?Q?=D0=90=D0=BB=D0=B5=D0=BA=D1=81=D0=B5=D0=B9_=D0=9A=D1=83=D1=82=D1=83=D0=B7=D0=BE=D0=B2?= <lexus.1995@mail.ru>
X-Priority: 3 (Normal)
Message-Id: <172562218.2016018095929@mail.ru>
To: =?utf-8?Q?=D0=90=D0=BB=D0=B5=D0=BA=D1=81=D0=B5=D0=B9?= <lexus.1995@mail.ru>
Cc: =?utf-8?Q?=D0=90=D0=BB=D0=B5=D0=BA=D1=81=D0=B5=D0=B9_CC?= <lexus27.khv@gmail.com>
Bcc: =?utf-8?Q?=D0=90=D0=BB=D0=B5=D0=BA=D1=81=D0=B5=D0=B9_BCC?= <lexus27.khv@gmail.com>
Subject: =?utf-8?Q??=
Mime-Version: 1.0
Content-Type: multipart/mixed; boundary="----=_568f5e512471d"
X-Mras: Ok

------=_568f5e512471d
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: 8bit

<a href="cid:text.html">Посмотрите это письмо</a>
------=_568f5e512471d
Content-Type: application/octet-stream; name="text.html"
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="text.html"

PGRpdiBzdHlsZT0iY29sb3I6cmVkIj7QrdGC0L4g0LLQu9C+0LbQtdC90LjQtSEuPC9kaXY
+

------=_568f5e512471d--
';

$doc = new Document();
$doc->setRaw($partitions);
//print_r($doc);
echo $doc->getRaw();*/

