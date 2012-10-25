<?php
namespace CdliTwoStageSignup\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    EmailOptionsInterface, InfrastructureOptionsInterface
{
    protected $storageAdapter = 'ZendDb';
    protected $emailFromAddress = '';
    protected $verificationEmailSubjectLine = 'Email Address Verification';

    public function setStorageAdapter($adapter)
    {
        $this->storageAdapter = $adapter;
        return $this;
    }

    public function getStorageAdapter()
    {
        return $this->storageAdapter;
    }

    public function setEmailFromAddress($email)
    {
        $this->emailFromAddress = $email;
        return $this;
    }

    public function getEmailFromAddress()
    {
        return $this->emailFromAddress;
    }

    public function setVerificationEmailSubjectLine($subject)
    {
        $this->verificationEmailSubjectLine = $subject;
        return $this;
    }

    public function getVerificationEmailSubjectLine()
    {
        return $this->verificationEmailSubjectLine;
    }
}
