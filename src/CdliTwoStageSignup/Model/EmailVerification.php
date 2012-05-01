<?php

namespace CdliTwoStageSignup\Model;

use ZfcBase\Model\ModelAbstract;

class EmailVerification extends ModelAbstract
{
	protected $requestKey;
	protected $emailAddress;
	protected $requestTime;

	public function setRequestKey($key)
	{
		$this->requestKey = $key;
		return $this;
	}

	public function getRequestKey()
	{
		return $this->requestKey;
	}

	public function generateRequestKey()
	{
		$this->setRequestKey(strtoupper(substr(sha1(
			$this->getEmailAddress() . 
			'####' . 
            $this->getRequestTime()->getTimestamp()
		),0,15)));
	}

	public function setEmailAddress($email)
	{
		$this->emailAddress = $email;
		return $this;
	}

	public function getEmailAddress()
	{
		return $this->emailAddress;
	}

	public function setRequestTime($time)
	{
        if ( ! $time instanceof \DateTime ) {
			$time = new \DateTime($time);
		}
		$this->requestTime = $time;
		return $this;
	}

	public function getRequestTime()
	{
		if ( ! $this->requestTime instanceof \DateTime ) {
			$this->requestTime = new \DateTime('now');
		}
		return $this->requestTime;
	}
}
