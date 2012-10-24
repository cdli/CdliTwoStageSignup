<?php
namespace CdliTwoStageSignupTest\Mapper;

use CdliTwoStageSignupTest\Framework\MapperTestCase;
use CdliTwoStageSignup\Mapper\EmailVerification as Mapper;
use CdliTwoStageSignup\Entity\EmailVerification as Entity;
use Zend\Db\Adapter\Adapter as DbAdapter;

class EmailVerificationTest extends MapperTestCase
{

    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set('GMT');

        $this->model = new Entity();
        $this->model->setEmailAddress('foo@bar.com');
        $this->model->setRequestTime(new \DateTime('2001-01-01T01:01:01'));
        $this->model->generateRequestKey();
        $this->mapper = $this->getServiceLocator()->get('cdlitwostagesignup_ev_modelmapper');
    }

    public function testPersist()
    {
        $this->mapper->insert($this->model);

        // Find the inserted record and verify it was created properly
        $result = $this->_queryFindByRequestKey($this->model->getRequestKey());
        $this->assertInternalType('array', $result);
        $this->assertEquals($this->model->getRequestKey(), $result['request_key']);
        $this->assertEquals($this->model->getEmailAddress(), $result['email_address']);
        $this->assertEquals($this->model->getRequestTime()->format('Y-m-d H:i:s'), $result['request_time']);
    }

    public function testRemove()
    {
        $this->importSchema(__DIR__ . '/_files/singlerecord.sql');
        $model = $this->mapper->remove($this->model);

        // Verify that it was removed
        $result = $this->_queryFindByRequestKey($this->model->getRequestKey());
        $this->assertFalse($result);
    }

    public function testFindByEmail()
    {
        $this->importSchema(__DIR__ . '/_files/singlerecord.sql');
        $model = $this->mapper->findByEmail('foo@bar.com');
        $this->assertEquals($this->model, $model);
    }

    public function testFindByRequestKey()
    {
        $this->importSchema(__DIR__ . '/_files/singlerecord.sql');
        $model = $this->mapper->findByRequestKey('DCE2D890895CF02');
        $this->assertEquals($this->model, $model);
    }

    public function testCleanExpiredVerificationRequests()
    {
        $this->importSchema(__DIR__ . '/_files/singlerecord.sql');

        // Add a second, non-expired request
        $m = new Entity();
        $m->setEmailAddress('bar@baz.com');
        $m->generateRequestKey();
        $this->mapper->insert($m);

        $this->mapper->cleanExpiredVerificationRequests();

        $set = $this->db->query('SELECT * FROM '.$this->db->platform->quoteIdentifier('user_signup_email_verification'))->execute();
        $this->assertEquals(1, $set->count());
        $actualEntity = $set->current();
        $this->assertEquals($m->getRequestKey(), $actualEntity['request_key']);
        $this->assertEquals($m->getEmailAddress(), $actualEntity['email_address']);
        $this->assertEquals($m->getRequestTime()->format('Y-m-d H:i:s'), $actualEntity['request_time']);
    }

    protected function _queryFindByRequestKey($key)
    {
        $stmt = $this->db->query('SELECT * FROM '.$this->db->platform->quoteIdentifier('user_signup_email_verification').' WHERE request_key = ' . $this->db->driver->formatParameterName('id'));
        $results = $stmt->execute(array('id'=>$key));
        return $results->current();
    }

}
