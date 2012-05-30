<?php

namespace CdliTwoStageSignup\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Http\Response,
    Zend\View\Model\ViewModel,
    CdliTwoStageSignup\Form\EmailVerification as EvrForm,
    CdliTwoStageSignup\Form\EmailVerificationFilter as EvrFilter,
    CdliTwoStageSignup\Model\EmailVerification as EvrModel,
    CdliTwoStageSignup\Service\EmailVerification as EvrService;

class RegisterController extends ActionController
{
    protected $emailVerificationForm = NULL;
    protected $emailVerificationFilter = NULL;
    protected $emailVerificationService = NULL;

    public function emailVerificationAction()
    {
        $this->getEmailVerificationService()->cleanExpiredVerificationRequests();

        $form = $this->getEmailVerificationForm();
        $form->setInputFilter($this->getEmailVerificationFilter());
        if ( $this->getRequest()->isPost() )
        {
            $form->setData($this->getRequest()->post());
            if ( $form->isValid() )
            {
                $service = $this->getEmailVerificationService();
                $model = $service->createFromForm($form);
                $service->sendVerificationEmailMessage($model);

                $vm = new ViewModel(array('record' => $model));
                $vm->setTemplate('cdli-twostagesignup/email-verification/sent');
                return $vm;
            }
        }

        // Render the form
        $vm = new ViewModel(array('form' => $form));
        $vm->setTemplate('cdli-twostagesignup/email-verification/form');
        return $vm;
    }

    public function checkTokenAction()
    {
        $this->getEmailVerificationService()->cleanExpiredVerificationRequests();

        $token = $this->getEvent()->getRouteMatch()->getParam('token');
        $validator = new \Zend\Validator\Hex();
        if ( $validator->isValid($token) )
        {
            $model = $this->getEmailVerificationService()->findByRequestKey($token);
            if ( $model instanceof EvrModel )
            {
                $locator = $this->getLocator();
                $formAction = $this->url()->fromRoute('zfcuser/register/step2', array('token'=>$model->getRequestKey()));

                // Listen for the form's init event
                $events = \Zend\EventManager\StaticEventManager::getInstance();
                $events->attach('ZfcUser\Form\Register','init', function($e) use ($model) {
                    $form = $e->getTarget();
                    // Replace the email address input field with a hidden field
                    $form->removeElement('email');
                    $form->addElement('hidden', 'email', array(
                        'value' => $model->getEmailAddress()
                    ));
                });

                // Listen for registration completion and delete the email verification record
                $service = $this->getEmailVerificationService();
                $zfcServiceEvents = $locator->get('ZfcUser\Service\User')->events();
                $zfcServiceEvents->attach('createFromForm', function($e) use ($service, $model) {
                    $service->delete($model);
                });

                // Hook into existing form processing logic
                $vm = $this->forward()->dispatch('zfcuser', array('action' => 'register'));
                if ( $vm instanceof Response )
                {
                    $zfcUserAction = $this->url()->fromRoute('zfcuser/register');

                    // Intercept form validation failure redirects from ZfcUser
                    $locationHeaders = $this->getResponse()->headers()->get('Location');
                    if ( count($locationHeaders) > 0 ) 
                    {
                        $shouldInterceptRedirect = false;
                        foreach ( $this->getResponse()->headers()->get('Location') as $header )
                            if ( $header == $zfcUserAction )
                                $shouldInterceptRedirect = true;
                        if ( !$shouldInterceptRedirect )
                            return $vm;
                    }

                    // If we get here, we must intercept, so reset the response
                    $response = $this->getResponse();
                    $response->setStatusCode(200);
                    $response->headers()->clearHeaders();

                    // ... and create a view model to render the form
                    $vm = new ViewModel(array(
                        'registerForm' => $this->getLocator()->get('ZfcUser\Form\Register')
                    ));
                }

                // Defeat ZfcUser's attempt to render it's own view script
                $vm->setVariable('formAction', $formAction);
                $vm->setVariable('record', $model);
                $vm->setTemplate('cdli-twostagesignup/register');
                return $vm;
            }
        }
        die('ERROR!');
    }

    public function getEmailVerificationForm()
    {
        if ($this->emailVerificationForm === null)
        {
            $this->emailVerificationForm = $this->getServiceLocator()->get('cdlitwostagesignup_ev_form');
        }
        return $this->emailVerificationForm;
    }

    public function setEmailVerificationForm(EvrForm $emailVerificationForm)
    {
        $this->emailVerificationForm = $emailVerificationForm;
        return $this;
    }

    public function getEmailVerificationFilter()
    {
        if ($this->emailVerificationFilter === null)
        {
            $this->emailVerificationFilter = $this->getServiceLocator()->get('cdlitwostagesignup_ev_filter');
        }
        return $this->emailVerificationFilter;
    }

    public function setEmailVerificationFilter(EvrFilter $emailVerificationFilter)
    {
        $this->emailVerificationFilter = $emailVerificationFilter;
        return $this;
    }

    public function getEmailVerificationService()
    {
        if ($this->emailVerificationService === null)
        {
            $this->emailVerificationService = $this->getServiceLocator()->get('cdlitwostagesignup_ev_service');
        }
        return $this->emailVerificationService;
    }

    public function setEmailVerificationService(EvrService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
        return $this;
    }
}
