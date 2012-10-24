<?php

namespace CdliTwoStageSignup\Entity;

class EmailVerification
{
    protected $request_key;
    protected $email_address;
    protected $request_time;

    public function setRequestKey($key)
    {
        $this->request_key = $key;
        return $this;
    }

    public function getRequestKey()
    {
        return $this->request_key;
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
        $this->email_address = $email;
        return $this;
    }

    public function getEmailAddress()
    {
        return $this->email_address;
    }

    public function setRequestTime($time)
    {
        if ( ! $time instanceof \DateTime ) {
            $time = new \DateTime($time);
        }
        $this->request_time = $time;
        return $this;
    }

    public function getRequestTime()
    {
        if ( ! $this->request_time instanceof \DateTime ) {
            $this->request_time = new \DateTime('now');
        }
        return $this->request_time;
    }

    public function isExpired()
    {
        $expiryDate = new \DateTime('24 hours ago');
        return $this->getRequestTime() < $expiryDate;
    }
}
