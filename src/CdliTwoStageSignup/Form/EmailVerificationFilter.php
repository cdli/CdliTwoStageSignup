<?php
namespace CdliTwoStageSignup\Form;

use Zend\InputFilter\InputFilter;

class EmailVerificationFilter extends InputFilter
{
    protected $uniqueEmailAddressValidator;

    public function __construct($uniqueEmailAddressValidator)
    {
        $this->setUniqueEmailAddressValidator($uniqueEmailAddressValidator);

        $this->add(array(
            'name'       => 'email',
            'required'   => true,
            'validators' => array(
                array( 'name' => 'EmailAddress' ),
                $this->getUniqueEmailAddressValidator()
            )
        ));
    }

    public function getUniqueEmailAddressValidator()
    {
        return $this->uniqueEmailAddressValidator;
    }
 
    public function setUniqueEmailAddressValidator($uniqueEmailAddressValidator)
    {
        $this->uniqueEmailAddressValidator = $uniqueEmailAddressValidator;
        return $this;
    }
}
