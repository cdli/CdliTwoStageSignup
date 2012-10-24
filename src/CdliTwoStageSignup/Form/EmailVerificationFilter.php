<?php
namespace CdliTwoStageSignup\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorInterface;

class EmailVerificationFilter extends InputFilter
{
    /**
     * Validator to ensure email address isn't already used by an existing account
     * @var ValidatorInterface
     */
    protected $uniqueEmailAddressValidator;

    /**
     * Validator to ensure email address isn't being used by an existing request
     * @var ValidatorInterface
     */
    protected $uniqueRegistrationSessionValidator;

    /**
     * Create instance of InputFilter
     * @param ValidatorInterface $uniqueEmailAddressValidator unique email address validator
     * @param ValidatorInterface $uniqueRegistrationSessionValidator unique registration session validator
     */
    public function __construct(ValidatorInterface $uniqueEmailAddressValidator, ValidatorInterface $uniqueRegistrationSessionValidator)
    {
        $this->setUniqueEmailAddressValidator($uniqueEmailAddressValidator);
        $this->setUniqueRegistrationSessionValidator($uniqueRegistrationSessionValidator);

        $this->add(array(
            'name'       => 'email_address',
            'required'   => true,
            'validators' => array(
                array( 'name' => 'EmailAddress' ),
                $this->getUniqueRegistrationSessionValidator(),
                $this->getUniqueEmailAddressValidator(),
            )
        ));
    }

    /**
     * Retrieve validator which enforces unique email address for each registered user account
     * @return ValidatorInterface
     */
    public function getUniqueEmailAddressValidator()
    {
        return $this->uniqueEmailAddressValidator;
    }
 
    /**
     * Set validator which enforces unique email address for each registered user account
     * @param ValidatorInterface $uniqueEmailAddressValidator
     * @return self
     */
    public function setUniqueEmailAddressValidator(ValidatorInterface $uniqueEmailAddressValidator)
    {
        $this->uniqueEmailAddressValidator = $uniqueEmailAddressValidator;
        return $this;
    }

    /**
     * Retrieve validator which enforces unique email address for each active registration request
     * @return ValidatorInterface
     */
    public function getUniqueRegistrationSessionValidator()
    {
        return $this->uniqueRegistrationSessionValidator;
    }
 
    /**
     * Set validator which enforces unique email address for each active registration request
     * @param ValidatorInterface $uniqueRegistrationSessionValidator
     * @return self
     */
    public function setUniqueRegistrationSessionValidator(ValidatorInterface $uniqueRegistrationSessionValidator)
    {
        $this->uniqueRegistrationSessionValidator = $uniqueRegistrationSessionValidator;
        return $this;
    }

}

