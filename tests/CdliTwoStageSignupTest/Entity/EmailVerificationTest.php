<?php
namespace CdliTwoStageSignupTest\Entity;

use CdliTwoStageSignupTest\Framework\TestCase;
use CdliTwoStageSignup\Entity\EmailVerification;

class EmailVerificationTest extends TestCase
{
    public function setUp()
    {
        $this->model = new EmailVerification();
        $this->model->setRequestTime(new \DateTime('2001-01-01T01:01:01+0100'));
        $this->initialState = $this->model->toArray();
    }

    public function testGetSetRequestKey()
    {
        $this->model->setRequestKey('foo');
        $this->assertEquals('foo',$this->model->getRequestKey());
        $expectedState = $this->initialState;
        $expectedState['requestKey'] = 'foo';
        $this->assertEquals($expectedState, $this->model->toArray());
    }

    public function testGetSetEmailAddress()
    {
        $this->model->setEmailAddress('foo@bar.com');
        $this->assertEquals('foo@bar.com',$this->model->getEmailAddress());
        $expectedState = $this->initialState;
        $expectedState['emailAddress'] = 'foo@bar.com';
        $this->assertEquals($expectedState, $this->model->toArray());
    }

    public function testGenerateRequestKey()
    {
        $this->model->setEmailAddress('foo@bar.com');
        $this->model->generateRequestKey();
        $this->assertEquals("B3F03B82574BDB5", $this->model->getRequestKey());
    }

    public function testGetSetRequestTime()
    {
        $objDate = new \DateTime('now');
        $this->model->setRequestTime($objDate);
        $this->assertEquals($objDate,$this->model->getRequestTime());
        $expectedState = $this->initialState;
        $expectedState['requestTime'] = $objDate;
        $this->assertEquals($expectedState, $this->model->toArray());
    }

    public function testIsExpired()
    {
        $this->assertTrue($this->model->isExpired());

        $this->model->setRequestTime(new \DateTime('30 seconds ago'));
        $this->assertFalse($this->model->isExpired());
    }

}
