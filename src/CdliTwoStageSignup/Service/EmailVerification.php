<?php

namespace CdliTwoStageSignup\Service;

use Zend\Form\Form;
use Zend\EventManager\ListenerAggregate;
use ZfcBase\EventManager\EventProvider;
use CdliTwoStageSignup\Entity\EmailVerification as Model;
use CdliTwoStageSignup\Mapper\EmailVerification\MapperInterface as EvrMapper;
use Zend\Mail\Message as EmailMessage;
use Zend\Mail\Transport\TransportInterface as EmailTransport;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface as ViewRenderer;
use CdliTwoStageSignup\Options\EmailOptionsInterface;
use CdliTwoStageSignup\Form\EmailVerification as EvrForm;

class EmailVerification extends EventProvider
{
    /**
     * @var EvrMapper
     */
    protected $evrMapper;
    protected $serviceLocator;
    protected $emailMessageOptions;
    protected $emailRenderer;
    protected $emailTransport;

    public function findByRequestKey($token)
    {
        return $this->getEmailVerificationMapper()->findByRequestKey($token);
    }

    public function findByEmail($email)
    {
        return $this->getEmailVerificationMapper()->findByEmail($email);
    }

    public function cleanExpiredVerificationRequests()
    {
        return $this->getEmailVerificationMapper()->cleanExpiredVerificationRequests();
    }

    public function remove(Model $m)
    {
        return $this->getEmailVerificationMapper()->remove($m);
    }

    /**
     * createFromForm
     *
     * @param Form $form
     * @return CdliTwoStageSignup\Model\EmailVerification
     */
    public function createFromForm(Form $form)
    {
        $form->bind(new Model());
        if (!$form->isValid()) {
            return false;
        }

        $model = $form->getData();
        $model->generateRequestKey();
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('record' => $model, 'form' => $form));
        $this->getEmailVerificationMapper()->insert($model);
        return $model;
    }

    public function sendVerificationEmailMessage(Model $record)
    {
        $fromAddress = $this->getEmailMessageOptions()->getEmailFromAddress();
        $subject = $this->getEmailMessageOptions()->getVerificationEmailSubjectLine();

        $message = new EmailMessage();
        $message->setFrom($fromAddress);
        $message->setTo($record->getEmailAddress());
        $message->setSubject($subject);

        $viewModel = new ViewModel(array('record' => $record));
        $viewModel->setTerminal(true)->setTemplate('cdli-twostagesignup/email/verification');
        $message->setBody($this->emailRenderer->render($viewModel));

        $this->emailTransport->send($message);
    }

    /**
     * setEmailVerificationMapper
     *
     * @param EvrMapper $evrMapper
     * @return User
     */
    public function setEmailVerificationMapper(EvrMapper $evrMapper)
    {
        $this->evrMapper = $evrMapper;
        return $this;
    }

    public function getEmailVerificationMapper()
    {
        return $this->evrMapper;
    }

    public function setEmailVerificationForm(EvrForm $f)
    {
        $this->evrForm = $f;
        return $this;
    }

    public function getEmailVerificationForm()
    {
        return $this->evrForm;
    }

    public function setMessageRenderer(ViewRenderer $emailRenderer)
    {
        $this->emailRenderer = $emailRenderer;
        return $this;
    }

    public function setMessageTransport(EmailTransport $emailTransport)
    {
        $this->emailTransport = $emailTransport;
        return $this;
    }

    public function getEmailMessageOptions()
    {
        return $this->emailMessageOptions;
    }

    public function setEmailMessageOptions(EmailOptionsInterface $opt)
    {
        $this->emailMessageOptions = $opt;
        return $this;
    }


}
