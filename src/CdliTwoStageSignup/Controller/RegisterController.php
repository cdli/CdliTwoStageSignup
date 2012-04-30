<?php

namespace CdliTwoStageSignup\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\View\Model\ViewModel;

class RegisterController extends ActionController
{
	public function emailValidationAction()
	{
		return new ViewModel();	
	}
}
