<?php
namespace CdliTwoStageSignupTest\Service;

use CdliTwoStageSignupTest\Framework\TestCase;
use CdliTwoStageSignup\Service\EmailVerification as Service;
use CdliTwoStageSignup\Entity\EmailVerification as Entity;
use CdliTwoStageSignup\Options\ModuleOptions;

class EmailVerificationTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set('GMT');

        $this->model = new Entity();
        $this->model->setEmailAddress('foo@bar.com');
        $this->model->setRequestTime(new \DateTime('2001-01-01T01:01:01'));
        $this->model->generateRequestKey();
    }

    public function testFindByRequestKey()
    {
        $evrMapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $evrMapper->expects($this->once())
                  ->method('findByRequestKey')
                  ->with($this->equalTo('DCE2D890895CF02'))
                  ->will($this->returnValue($this->model));

        $service = new Service();
        $service->setEmailVerificationMapper($evrMapper);
        $result = $service->findByRequestKey('DCE2D890895CF02');
        $this->assertEquals($this->model, $result);
    }

    public function testFindByEmail()
    {
        $evrMapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $evrMapper->expects($this->once())
                  ->method('findByEmail')
                  ->with($this->equalTo('foo@bar.com'))
                  ->will($this->returnValue($this->model));

        $service = new Service();
        $service->setEmailVerificationMapper($evrMapper);
        $result = $service->findByEmail('foo@bar.com');
        $this->assertEquals($this->model, $result);
    }

    public function testCleanExpiredVerificationRequests()
    {
        $evrMapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $evrMapper->expects($this->once())
                  ->method('cleanExpiredVerificationRequests')
                  ->with($this->anything())
                  ->will($this->returnValue(NULL));

        $service = new Service();
        $service->setEmailVerificationMapper($evrMapper);
        $service->cleanExpiredVerificationRequests();
    }

    public function testRemove()
    {
        $evrMapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $evrMapper->expects($this->once())
                  ->method('remove')
                  ->with($this->model)
                  ->will($this->returnValue(1));

        $service = new Service();
        $service->setEmailVerificationMapper($evrMapper);
        $this->assertEquals(1, $service->remove($this->model));
    }

    public function testCreateFromForm()
    {
        $form = $this->getMock('Zend\Form\Form');
        $form->expects($this->any())
             ->method('getData')
             ->will($this->returnValue(array('email'=>'foo@bar.com')));

        $evrMapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $evrMapper->expects($this->once())
                  ->method('persist')
                  ->with($this->isInstanceOf('CdliTwoStageSignup\Entity\EmailVerification'))
                  ->will($this->returnValue(NULL));

        $service = new Service();
        $service->setEmailVerificationMapper($evrMapper);
        $result = $service->createFromForm($form);
        $this->assertInstanceOf('CdliTwoStageSignup\Entity\EmailVerification', $result);
    }

    public function testSendVerificationEmailMessage()
    {
        $sentMessage = "";

        $viewRenderer = $this->getMock('Zend\View\Renderer\RendererInterface');
        $viewRenderer->expects($this->once())
                     ->method('render')
                     ->with($this->isInstanceOf('Zend\View\Model\ViewModel'))
                     ->will($this->returnCallback(function($m) { 
                        return $m->record->getRequestKey() . ' ' . $m->record->getEmailAddress();
                     }));
        $this->getServiceLocator()->setService('Zend\View\Renderer\PhpRenderer', $viewRenderer);

        $transport = $this->getMock('Zend\Mail\Transport\TransportInterface');
        $transport->expects($this->once())
                  ->method('send')
                  ->with($this->isInstanceOf('Zend\Mail\Message'))
                  ->will($this->returnCallback(function($m) use (&$sentMessage) {
                      $sentMessage = $m;
                  }));
        $this->getServiceLocator()->setService('Zend\Mail\Transport\TransportInterface', $transport);

        $service = $this->getServiceLocator()->get('cdlitwostagesignup_ev_service');
        $service->setMessageRenderer($viewRenderer);
        $service->setMessageTransport($transport);
        $service->sendVerificationEmailMessage($this->model); 
        $this->assertEquals(
            $this->model->getRequestKey() . ' ' . $this->model->getEmailAddress(),
            $sentMessage->getBody()
        );
    }

}
