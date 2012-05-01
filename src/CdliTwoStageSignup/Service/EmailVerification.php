<?php

namespace CdliTwoStageSignup\Service;

use Zend\Form\Form,
    Zend\EventManager\ListenerAggregate,
    DateTime,
    CdliTwoStageSignup\Module as modCTSS,
    ZfcBase\EventManager\EventProvider,
	CdliTwoStageSignup\Model\EmailVerification as Model,
	CdliTwoStageSignup\Model\EmailVerificationMapper as ModelMapper,
    Zend\Mail\Message as EmailMessage,
    Zend\Mail\Transport as EmailTransport,
    Zend\View\Model\ViewModel,
    Zend\View\Renderer as ViewRenderer;

class EmailVerification extends EventProvider
{
    /**
     * @var ModelMapper
     */
    protected $evrMapper;

    protected $emailRenderer;
    protected $emailTransport;

    public function findByRequestKey($token)
	{
		return $this->evrMapper->findByRequestKey($token);
	}

    public function findByEmail($email)
	{
		return $this->evrMapper->findByEmail($email);
	}

    public function cleanExpiredVerificationRequests()
	{
		return $this->evrMapper->cleanExpiredVerificationRequests();
	}

    /**
     * createFromForm
     *
     * @param Form $form
     * @return CdliTwoStageSignup\Model\EmailVerification
     */
    public function createFromForm(Form $form)
    {
        $model = new Model();
        $model->setEmailAddress($form->getValue('email'));
        $model->setRequestTime(new DateTime('now'));
        $model->generateRequestKey();
        $this->events()->trigger(__FUNCTION__, $this, array('record' => $model, 'form' => $form));
        $this->evrMapper->add($model);
        return $model;
    }

    public function sendVerificationEmailMessage(Model $record)
    {
		$message = new EmailMessage();
        $message->setFrom(modCTSS::getOption('email_from_address'));
        $message->setTo($record->getEmailAddress());
        $message->setSubject(modCTSS::getOption('verification_email_subject_line'));

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
}
