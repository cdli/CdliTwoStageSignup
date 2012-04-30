<?php

namespace CdliTwoStageSignup\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\View\Model\ViewModel,
    CdliTwoStageSignup\Form\EmailVerification as EmailVerificationForm;

class RegisterController extends ActionController
{
	protected $emailVerificationForm = NULL;

	public function emailValidationAction()
	{
		$form = $this->getEmailVerificationForm();

		if ( $this->getRequest()->isPost() )
		{
			$data = $this->getRequest()->post()->toArray();
			if ( $form->isValid($data) )
			{
				echo '<div class="well">';
				print_r($data);
				echo '</div>';
			}
		}

		// Render the form
		$vm = new ViewModel(array('form' => $form));
		$vm->setTemplate('cdli-twostagesignup/email-verification');
		return $vm;
	}

    public function getEmailVerificationForm()
	{
		return $this->emailVerificationForm;
	}

    public function setEmailVerificationForm(EmailVerificationForm $emailVerificationForm)
	{
		$this->emailVerificationForm = $emailVerificationForm;
		return $this;
	}
}
