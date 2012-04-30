<?php

namespace CdliTwoStageSignup\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\View\Model\ViewModel,
    CdliTwoStageSignup\Form\EmailVerification as EvrForm,
    CdliTwoStageSignup\Model\EmailVerification as EvrModel,
    CdliTwoStageSignup\Service\EmailVerification as EvrService;

class RegisterController extends ActionController
{
	protected $emailVerificationForm = NULL;
	protected $emailVerificationService = NULL;

	public function emailValidationAction()
	{
		$form = $this->getEmailVerificationForm();

		if ( $this->getRequest()->isPost() )
		{
			$data = $this->getRequest()->post()->toArray();
			if ( $form->isValid($data) )
			{
                $model = $this->getEmailVerificationService()->createFromForm($form);
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

    public function setEmailVerificationForm(EvrForm $emailVerificationForm)
	{
		$this->emailVerificationForm = $emailVerificationForm;
		return $this;
	}

    public function getEmailVerificationService()
	{
		return $this->emailVerificationService;
	}

    public function setEmailVerificationService(EvrService $emailVerificationService)
	{
		$this->emailVerificationService = $emailVerificationService;
		return $this;
	}
}
