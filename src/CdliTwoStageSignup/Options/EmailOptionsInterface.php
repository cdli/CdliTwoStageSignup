<?php
namespace CdliTwoStageSignup\Options;

use Zend\Stdlib\AbstractOptions;

interface EmailOptionsInterface
{
    public function setEmailFromAddress($email);
    public function getEmailFromAddress();
    public function setVerificationEmailSubjectLine($subject);
    public function getVerificationEmailSubjectLine();
}
