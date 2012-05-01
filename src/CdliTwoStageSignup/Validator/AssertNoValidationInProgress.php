<?php

namespace CdliTwoStageSignup\Validator;

use Zend\Validator\AbstractValidator,
    CdliTwoStageSignup\Model\EmailVerificationMapper;

class AssertNoValidationInProgress extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
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
     * @var string
     */
    protected $key;

    /**
     * Required options are:
     *  - key     Field to use, 'emial' or 'username'
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('key', $options)) {
            throw new Exception\InvalidArgumentException('No key provided');
        }
        
        $this->setKey($options['key']);
        
        parent::__construct($options);
    }

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

    /**
     * Get key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
 
    /**
     * Set key.
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
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
