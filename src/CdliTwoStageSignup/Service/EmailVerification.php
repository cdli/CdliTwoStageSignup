<?php

namespace CdliTwoStageSignup\Service;

use Zend\Form\Form,
    Zend\EventManager\ListenerAggregate,
    DateTime,
    CdliTwoStageSignup\Module as modCTSS,
    ZfcBase\EventManager\EventProvider,
	CdliTwoStageSignup\Model\EmailVerification as Model,
	CdliTwoStageSignup\Model\EmailVerificationMapper as ModelMapper;

class EmailVerification extends EventProvider
{
    /**
     * @var ModelMapper
     */
    protected $evrMapper;

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
}
