<?php

namespace CdliTwoStageSignup\Form;

use Zend\Form\Form,
    Zend\Form\Element\Csrf,
    ZfcUser\Mapper\UserInterface as UserMapper,
    ZfcBase\Form\ProvidesEventsForm;

class EmailVerification extends ProvidesEventsForm
{
    public function __construct()
    {
        parent::__construct();

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'label' => 'Email Address',
                'type' => 'text',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Verify Email Address',
                'type' => 'submit',
            ),
        ));

        $this->add(new Csrf('csrf'));

        $this->events()->trigger('init', $this);
    }
}
