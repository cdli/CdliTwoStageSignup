<?php
namespace CdliTwoStageSignup\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    EmailOptionsInterface
{
    protected $emailFromAddress = '';
    protected $verificationEmailSubjectLine = 'Email Address Verification';

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
