<?php
namespace CdliTwoStageSignupTest\Model;

use CdliTwoStageSignupTest\Framework\MapperTestCase;
use CdliTwoStageSignup\Model\EmailVerificationMapper as Mapper;
use CdliTwoStageSignup\Model\EmailVerification as Model;
use Zend\Db\Adapter\Adapter as DbAdapter;

class EmailVerificationMapperTest extends MapperTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->model = new Model();
        $this->model->setEmailAddress('foo@bar.com');
        $this->model->setRequestTime(new \DateTime('2001-01-01T01:01:01+0100'));
        $this->model->generateRequestKey();
        $this->mapper = $this->getLocator()->get('CdliTwoStageSignup\Model\EmailVerificationMapper');
    }

    public function testAdd()
    {
        $this->mapper->add($this->model);

        // Find the inserted record and verify it was created properly
        $stmt = $this->db->query('SELECT * FROM '.$this->db->platform->quoteIdentifier('user_signup_email_verification').' WHERE request_key = ' . $this->db->driver->formatParameterName('id'));
        $results = $stmt->execute(array('id'=>$this->model->getRequestKey()));
        $result = $results->current();
        $this->assertInternalType('array', $result);
        $this->assertEquals($this->model->getRequestKey(), $result['request_key']);
        $this->assertEquals($this->model->getEmailAddress(), $result['email_address']);
        $this->assertEquals($this->model->getRequestTime()->format('Y-m-d H:i:s'), $result['request_time']);
    }

    public function testFindByEmail()
    {
        $this->importSchema(__DIR__ . '/_files/singlerecord.sql');
        $model = $this->mapper->findByEmail('foo@bar.com');
        $this->assertEquals($this->model, $model);
    }

}
