<?php

namespace Polonairs\SmsiBundle\Smsi;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SmscService
{
	const REQUEST_EMPTY = "EMPTY";
	const REQUEST_STD = "STD";

	private $login;
	private $password;
	private $logger;
	private $curlHandler;
	private $container;
	private $receiver;

	public function __construct($login, $password, $receiver, $logger, ContainerInterface $container)
	{
		$this->login = $login;
		$this->password = $password;
		$this->logger = $logger;
		$this->container = $container;
		$this->receiver = $receiver;

		$this->curlHandler = curl_init();
		curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlHandler, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($this->curlHandler, CURLOPT_TIMEOUT, 60);
		curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER, 0);
	}
	public function send(SmsMessage $message)
	{
		$data = $this->createRequest();

		$data["phones"] = $message->getReceiver();
		$data["mes"] = $message->getText();
		$data["sender"] = $message->getFrom();
		$result = $this->_send($data);
		//dump($data);
		if (isset($result["error"]))
		{
			$message->setError($result["error_code"]);
			if (isset($result["id"])) $message->setId($result["id"]);
		}
		else
		{
			$message->setId($result["id"]);
			$message->setCount($result["cnt"]);
			$message->setCost($result["cost"]);
			$this->balance = $result["balance"];
		}
	}
	public function cost(SmsMessage $message){}
	public function getBalance(){}
	private function createRequest($type = self::REQUEST_STD)
	{
		switch($type)
		{
			case self::REQUEST_EMPTY:
				return [
					"login"   => null, "psw"    => null, "phones"   => null, "mes"    => null,
					"id"      => null, "sender" => null, "translit" => null, "time"   => null,
					"tz"      => null, "period" => null, "freq"     => null, "flash"  => null,
					"bin"     => null, "push"   => null, "hlr"      => null, "ping"   => null,
					"mms"     => null, "mail"   => null, "call"     => null, "voice"  => null,
					"param"   => null, "subj"   => null, "charset"  => null, "cost"   => null,
					"fmt"     => null, "list"   => null, "valid"    => null, "maxsms" => null,
					"imgcode" => null, "userip" => null, "err"      => null, "op"     => null,
					"pp"      => null];
			case self::REQUEST_STD:
				$result = $this->createRequest(self::REQUEST_EMPTY);
				$result["login"] = $this->login;
				$result["psw"] = $this->password;
				$result["charset"] = "utf-8";
				$result["cost"] = 3;
				$result["fmt"] = 3;
				return $result;
		}
	}
	private function _send($data)
	{
		$url = "http://smsc.ru/sys/send.php";
		return $this->_exec($url, $data);
	}
	private function isDev()
	{
		return in_array($this->container->getParameter("kernel.environment"), ['test', 'dev'])
	}
	private function _exec($url, array $data)
	{
		$post = "";
		$data["fmt"] = 3;
		if( $this->isDev() && $this->receiver !== null)
		{
			$data["mes"] = "to [" . $data["phones"]. "]: " . $data["mes"];
			$data["phones"] = $this->receiver;
		}
		foreach($data as $k => $v) 
		{
			if ($v !== null) $post .= "$k=" . urlencode($v) . "&";
		}
		$post = substr($post, 0, -1);
		curl_setopt($this->curlHandler, CURLOPT_POST, 1);
		curl_setopt($this->curlHandler, CURLOPT_URL, $url);
		curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $post);
		if (($this->isDev() && $this->receiver !== null) || (!$this->isDev()))
		{
			$ret = curl_exec($this->curlHandler);
			$ret = json_encode($ret);
		}
		else
		{
			$ret = [ "id" => -1, "cnt" => 0, "cost" => 0, "balance" => 0 ];
		}
		$this->logger->info("sent: {$post}; got: {$ret}");
		return json_decode($ret, true);
	}
}