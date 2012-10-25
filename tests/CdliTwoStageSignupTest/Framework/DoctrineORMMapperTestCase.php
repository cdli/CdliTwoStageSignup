<?php

namespace CdliTwoStageSignupTest\Framework;

use Zend\Db\Adapter\Adapter;

class DoctrineORMMapperTestCase extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->dbSchemaDown();
        $this->dbSchemaUp();
    }

    protected function dbSchemaDown()
    {
        $this->importSchema(__DIR__ . '/../../../data/' . $this->getOptions()->getDatabaseSchemaDown());
    }

    protected function dbSchemaUp()
    {
        $this->importSchema(__DIR__ . '/../../../data/' . $this->getOptions()->getDatabaseSchemaUp());
    }

    protected function importSchema($file)
    {
        $conn = $this->getDBALConnection();
        $sqlfile = explode(';',file_get_contents($file));
        foreach ( $sqlfile as $sqlStmt ) {
            $sqlStmt = trim($sqlStmt);
            if ( !empty($sqlStmt) ) {
                $conn->executeQuery($sqlStmt);
            }
        }
    }

    protected $em;
    protected function getEntityManager()
    {
        if (is_null($this->em)) {
            $this->em = $this->getServiceLocator()->get('zfcuser_doctrine_em');
        }
        return $this->em;
    }

    protected $conn;
    protected function getDBALConnection()
    {
        if (is_null($this->conn)) {
            $this->conn = $this->getEntityManager()->getConnection();
        }
        return $this->conn;
    }

}
