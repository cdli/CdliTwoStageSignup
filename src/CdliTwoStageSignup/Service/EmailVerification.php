<?php

namespace CdliTwoStageSignup\Service;

use Zend\Form\Form,
    Zend\EventManager\ListenerAggregate,
    ZfcBase\EventManager\EventProvider,
    CdliTwoStageSignup\Entity\EmailVerification as Model,
    CdliTwoStageSignup\Mapper\EmailVerification as ModelMapper,
    Zend\Mail\Message as EmailMessage,
    Zend\Mail\Transport\TransportInterface as EmailTransport,
    Zend\View\Model\ViewModel,
    Zend\View\Renderer\RendererInterface as ViewRenderer;
use CdliTwoStageSignup\Options\EmailOptionsInterface;

class EmailVerification extends EventProvider
{
    /**
     * @var ModelMapper
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
        $data = $form->getData();

        $model = new Model();
        $model->setEmailAddress($data['email']);
        $model->setRequestTime(new \DateTime('now'));
        $model->generateRequestKey();
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('record' => $model, 'form' => $form));
        $this->getEmailVerificationMapper()->persist($model);
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
     * @param ModelMapper $evrMapper
     * @return User
     */
    public function setEmailVerificationMapper(ModelMapper $evrMapper)
    {
        $this->evrMapper = $evrMapper;
        return $this;
    }

    public function getEmailVerificationMapper()
    {
        return $this->evrMapper;
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
