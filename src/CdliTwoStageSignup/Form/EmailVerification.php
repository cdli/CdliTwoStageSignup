<?php

namespace CdliTwoStageSignup\Form;

use Zend\Form\Form,
    ZfcUser\Mapper\UserInterface as UserMapper,
    ZfcBase\Form\ProvidesEventsForm;

class EmailVerification extends ProvidesEventsForm
{
    protected $emailValidator;

    public function initLate()
    {
        $this->setMethod('post');

        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
                $this->emailValidator,
            ),
            'required'   => true,
            'label'      => 'Email',
            'order'      => 200,
        ));

        $this->addElement('submit', 'submit', array(
            'label'    => 'Verify Email Address',
            'ignore'   => true,
            'order'    => 1000,
        ));

        $this->addElement('hash', 'csrf', array(
            'ignore'     => true,
            'decorators' => array('ViewHelper'),
            'order'      => -100,
        ));

        $this->events()->trigger('init', $this);
    }

    public function setEmailValidator($emailValidator)
    {
        $this->emailValidator = $emailValidator;
        $this->initLate();  //Yuck
        return $this;
    }
}
