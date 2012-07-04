<?php

namespace CdliTwoStageSignup\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\Http\Response,
    Zend\View\Model\ViewModel,
    CdliTwoStageSignup\Form\EmailVerification as EvrForm,
    CdliTwoStageSignup\Form\EmailVerificationFilter as EvrFilter,
    CdliTwoStageSignup\Entity\EmailVerification as EvrModel,
    CdliTwoStageSignup\Service\EmailVerification as EvrService;

class RegisterController extends AbstractActionController
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
            $form->setData($this->getRequest()->getPost());
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
        $vm = new ViewModel(array(
            'form'               => $form,
            'enableRegistration' => $this->getServiceLocator()->get('zfcuser_module_options')->getEnableRegistration()
        ));
        $vm->setTemplate('cdli-twostagesignup/email-verification/form');
        return $vm;
    }

    public function checkTokenAction()
    {
        $this->getEmailVerificationService()->cleanExpiredVerificationRequests();

        $token = $this->plugin('params')->fromRoute('token');
        $validator = new \Zend\Validator\Hex();
        if ( $validator->isValid($token) )
        {
            $model = $this->getEmailVerificationService()->findByRequestKey($token);
            if ( $model instanceof EvrModel )
            {
                $locator = $this->getServiceLocator();
                $formAction = $this->url()->fromRoute('zfcuser/register/step2', array('token'=>$model->getRequestKey()));

                // Listen for the form's init event
                $events = \Zend\EventManager\StaticEventManager::getInstance();
                $events->attach('ZfcUser\Form\Register','init', function($e) use ($model) {
                    $form = $e->getTarget();
                    $form->get('email')->setLabel('')->setAttributes(array(
                        'type' => 'hidden',
                        'value' => $model->getEmailAddress(),
                    ));
                });

                // Listen for registration completion and delete the email verification record
                $service = $this->getEmailVerificationService();
                $zfcServiceEvents = $locator->get('zfcuser_user_service')->getEventManager();
                $zfcServiceEvents->attach('register', function($e) use ($service, $model) {
                    $service->remove($model);
                });

                // Hook into existing form processing logic
                $vm = $this->forward()->dispatch('zfcuser', array('action' => 'register'));
                if ( $vm instanceof Response )
                {
                    $zfcUserAction = $this->url()->fromRoute('zfcuser/register');
                    $stepTwoRoute = $this->url()->fromRoute('zfcuser/register/step2', array('token' => $token));

                    // Intercept form validation failure redirects from ZfcUser
                    $allHeaders = $this->getResponse()->getHeaders();
                    $locationHeader = $allHeaders->get('Location');
                    if ( $locationHeader->getUri() == $zfcUserAction ) {
                        $locationHeader->setUri($stepTwoRoute);
                    }
                    return $vm;
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
