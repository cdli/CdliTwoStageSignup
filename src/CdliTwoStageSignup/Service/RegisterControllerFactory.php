<?php
namespace CdliTwoStageSignup\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CdliTwoStageSignup\Controller\RegisterController;

class RegisterControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $evrForm    = $sm->get('cdlitwostagesignup_ev_form');
        $evrService = $sm->get('cdlitwostagesignup_ev_service');

        $controller = new RegisterController();
        $controller->setEmailVerificationForm($evrForm);
        $controller->setEmailVerificationService($evrService);
        return $controller;
    }
}
