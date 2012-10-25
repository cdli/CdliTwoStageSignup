<?php
namespace CdliTwoStageSignupTest\Mapper;

use CdliTwoStageSignupTest\Framework\DoctrineORMMapperTestCase;
use CdliTwoStageSignup\Entity\EmailVerification as Entity;
use Zend\ServiceManager\ServiceManager;

class DoctrineORMMapperTest extends DoctrineORMMapperTestCase
{

    public function setUp()
    {
        parent::setUp();

        if (!$this->getOptions()->getEnableDoctrineOrmTests()) {
            $this->markTestSkipped('Doctrine ORM mapper tests are disabled');
        }

        // Override the selected backend adapter
        $sl = $this->getServiceLocator();
        if ($sl instanceof ServiceManager) {
            $sl->setAllowOverride(true);
            $sl->setAlias('cdlitwostagesignup_ev_modelmapper', 'cdlitwostagesignup_ev_modelmapper_doctrineorm');
        }

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
        $this->assertInstanceOf('CdliTwoStageSignup\Entity\EmailVerification', $result);
        $this->assertEquals($this->model->getRequestKey(), $result->getRequestKey());
        $this->assertEquals($this->model->getEmailAddress(), $result->getEmailAddress());
        $this->assertEquals($this->model->getRequestTime(), $result->getRequestTime());
    }

    public function testRemove()
    {
        $this->importSchema(__DIR__ . '/_files/singlerecord.sql');
        $model = $this->_queryFindByRequestKey('DCE2D890895CF02');
        $model = $this->mapper->remove($model);

        // Verify that it was removed
        $result = $this->_queryFindByRequestKey($this->model->getRequestKey());
        $this->assertNull($result);
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

        $set = $this->getDBALConnection()->executeQuery('SELECT * FROM user_signup_email_verification')->fetchAll();
        $this->assertEquals(1, count($set));
        $actualEntity = array_pop($set);
        $this->assertEquals($m->getRequestKey(), $actualEntity['request_key']);
        $this->assertEquals($m->getEmailAddress(), $actualEntity['email_address']);
        $this->assertEquals($m->getRequestTime()->format('Y-m-d H:i:s'), $actualEntity['request_time']);
    }

    protected function _queryFindByRequestKey($key)
    {
        $repo = $this->em->getRepository('CdliTwoStageSignup\Entity\EmailVerification');
        return $repo->findOneBy(array('request_key' => $key));
    }

}
