<?php

namespace CdliTwoStageSignup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;
use CdliTwoStageSignup\Form\EmailVerification as EvrForm;
use CdliTwoStageSignup\Form\EmailVerificationFilter as EvrFilter;
use CdliTwoStageSignup\Entity\EmailVerification as EvrModel;
use CdliTwoStageSignup\Service\EmailVerification as EvrService;
use ZfcUser\Options\ModuleOptions as ZfcUserOptions;

class RegisterController extends AbstractActionController
{
    protected $emailVerificationForm = NULL;
    protected $emailVerificationService = NULL;
    protected $zfcUserOptions = NULL;

    public function emailVerificationAction()
    {
        $service = $this->getEmailVerificationService();
        $form = $this->getEmailVerificationForm();

        $service->cleanExpiredVerificationRequests();

        // Render the form page for rendering
        $formViewModel = new ViewModel(array(
            'form'               => $form,
            'enableRegistration' => $this->getZfcUserOptions()->getEnableRegistration()
        ));
        $formViewModel->setTemplate('cdli-twostagesignup/email-verification/form');

        // Process form submissions using the POST-Redirect-GET (PRG) plugin 
        $prg = $this->prg($this->url()->fromRoute('zfcuser/register'), true);
        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return $formViewModel;
        }

        // Attempt to process the form
        $form->setData($prg);
        $model = $service->createFromForm($form);
        if (!$model) {
            return $formViewModel;
        }
        $service->sendVerificationEmailMessage($model);

        $vm = new ViewModel(array('record' => $model));
        $vm->setTemplate('cdli-twostagesignup/email-verification/sent');
        return $vm;
    }

    public function checkTokenAction()
    {
        $service = $this->getEmailVerificationService();
        $events = $this->getServiceLocator()->get('SharedEventManager');

        $service->cleanExpiredVerificationRequests();

        // Pull and validate the Request Key
        $token = $this->plugin('params')->fromRoute('token');
        $validator = new \Zend\Validator\Hex();
        if ( !$validator->isValid($token) ) {
            throw new \InvalidArgumentException('Invalid Token!');
        }
 
        // Find the request key in ze database
        $model = $service->findByRequestKey($token);
        if ( ! $model instanceof EvrModel ) {
            throw new \InvalidArgumentException('Invalid Token!');
        }

        // Listen for registration completion and delete the email verification record if the
        // user account was registered successfully
        $events->attach('ZfcUser\Service\User', 'register.post', function($e) use ($service, $model) {
            $user = $e->getParam('user');
            if ($user instanceof \ZfcUser\Entity\UserInterface && !is_null($user->getID())) {
                $service->remove($model);
            }
        });


        // Ensure that the email address wasn't changed on the client side before POSTing
        if ($this->getRequest()->isPost()) {
            $this->getRequest()->getPost()->set('email', $model->getEmailAddress());
        }

        // Hook into existing form processing logic
        $vm = $this->forward()->dispatch('zfcuser', array('action' => 'register'));
        if ( $vm instanceof Response )
        {
            $zfcUserAction = $this->url()->fromRoute('zfcuser/register');
            $stepTwoRoute = $this->url()->fromRoute('zfcuser/register/step2', array('token' => $token));

            // Intercept form validation failure redirects from ZfcUser and change the URI
            // to point to this controller action
            $allHeaders = $this->getResponse()->getHeaders();
            $locationHeader = $allHeaders->get('Location');
            if ( $locationHeader->getUri() == $zfcUserAction ) {
                $locationHeader->setUri($stepTwoRoute);
            }
            return $vm;
        }

        // Defeat ZfcUser's attempt to render it's own view script
        // (necessary because it doesn't allow changing the form action)
        $vm->setVariable('model', $model);
        $vm->setTemplate('cdli-twostagesignup/register');
        return $vm;

    }

    public function getZfcUserOptions()
    {
        if ($this->zfcUserOptions === null)
        {
            $this->zfcUserOptions = $this->getServiceLocator()->get('zfcuser_module_options');
        }
        return $this->zfcUserOptions;
    }

    public function setZfcUserOptions(ZfcUserOptions $o)
    {
        $this->zfcUserOptions = $o;
        return $this;
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
