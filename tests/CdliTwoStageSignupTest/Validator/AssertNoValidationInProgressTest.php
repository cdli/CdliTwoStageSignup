<?php
namespace CdliTwoStageSignupTest\Validator;

use CdliTwoStageSignupTest\Framework\TestCase;
use CdliTwoStageSignup\Entity\EmailVerification as Entity;
use CdliTwoStageSignup\Validator\AssertNoValidationInProgress as SUT;

class AssertNoValidationInProgressTest extends TestCase
{
    public function setUp()
    {
        $this->model = new Entity();
        $this->model->setEmailAddress('foo@bar.com');

        $this->validator = new SUT(array()); 
    }

    public function testMapperFindsNoMatchingRecord()
    {
        $this->model->setRequestTime(new \DateTime('2001-01-01T01:01:01'));
        $this->model->generateRequestKey();

        $mapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $mapper->expects($this->once())
               ->method('findByEmail')
               ->with($this->equalTo('foo@bar.com'))
               ->will($this->returnValue(NULL));

        $this->validator->setMapper($mapper);
        $this->assertTrue($this->validator->isValid($this->model->getEmailAddress()));
        $messages = $this->validator->getMessages();
        $this->assertArrayNotHasKey('recordFound', $messages);
    }

    public function testMapperFindsMatchingRecordWhichIsExpired()
    {
        $this->model->setRequestTime(new \DateTime('2001-01-01T01:01:01'));
        $this->model->generateRequestKey();

        $mapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $mapper->expects($this->once())
               ->method('findByEmail')
               ->with($this->equalTo('foo@bar.com'))
               ->will($this->returnValue($this->model));

        $this->validator->setMapper($mapper);
        $this->assertTrue($this->validator->isValid($this->model->getEmailAddress()));
        $messages = $this->validator->getMessages();
        $this->assertArrayNotHasKey('recordFound', $messages);
    }


    public function testMapperFindsMatchingRecordWhichIsNotExpired()
    {
        $this->model->setRequestTime(new \DateTime('now'));
        $this->model->generateRequestKey();

        $mapper = $this->getMock('CdliTwoStageSignup\Mapper\EmailVerification');
        $mapper->expects($this->once())
               ->method('findByEmail')
               ->with($this->equalTo('foo@bar.com'))
               ->will($this->returnValue($this->model));

        $this->validator->setMapper($mapper);
        $this->assertFalse($this->validator->isValid($this->model->getEmailAddress()));
        $messages = $this->validator->getMessages();
        $this->assertInternalType('array', $messages);
        $this->assertArrayHasKey('recordFound', $messages);
    }
}
