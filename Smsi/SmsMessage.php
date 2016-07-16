<?php

namespace Polonairs\SmsiBundle\Smsi;

class SmsMessage
{
	private $id = null;
	private $to = null;
	private $from = null;
	private $text = null;
	private $status = null;
	private $count = null;
	private $cost = null;
	private $error = null;

	public function __construct(){ }

	public function getReceiver()
	{
		return $this->to;
	}
	public function getFrom()
	{
		return $this->from;
	}
	public function getText()
	{
		return $this->text;
	}
	public function getStatus()
	{
		return $this->status;
	}
	public function getCost()
	{
		return $this->cost;
	}

	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	public function setTo($to)
	{
		$this->to = $to;
		return $this;
	}
	public function setFrom($from)
	{
		$this->from = $from;
		return $this;
	}
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}
	public function setError($error)
	{
		$this->error = $error;
		return $this;
	}
	public function setCount($cnt)
	{
		$this->count = $cnt;
		return $this;
	}
	public function setCost($cost)
	{
		$this->cost = $cost;
		return $this;
	}
}
