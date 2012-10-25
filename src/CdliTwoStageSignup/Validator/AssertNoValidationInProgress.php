<?php

namespace CdliTwoStageSignup\Validator;

use Zend\Validator\AbstractValidator;
use CdliTwoStageSignup\Mapper\EmailVerification\MapperInterface as EmailVerificationMapper;

class AssertNoValidationInProgress extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_RECORD_FOUND    => "This email address already has a validation in progress",
    );

    /**
     * @var EmailVerificationMapper
     */
    protected $mapper;

    /**
     * getMapper 
     * 
     * @return EmailVerificationMapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * setMapper 
     * 
     * @param EmailVerificationMapper $mapper 
     * @return Db
     */
    public function setMapper(EmailVerificationMapper $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    public function isValid($value)
    {
        $valid = true;
        $this->setValue($value);

        $result = $this->getMapper()->findByEmail($value);
        if ($result && !$result->isExpired()) {
            $valid = false;
            $this->error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }

}
