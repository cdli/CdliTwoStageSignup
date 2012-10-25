<?php

namespace CdliTwoStageSignupTest\Framework;

use Zend\Db\Adapter\Adapter;

class DoctrineORMMapperTestCase extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->db = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
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
        $sqlfile = explode(';',file_get_contents($file));
        foreach ( $sqlfile as $sqlStmt ) {
            $sqlStmt = trim($sqlStmt);
            if ( !empty($sqlStmt) ) {
                $this->db->query($sqlStmt)->execute();
            }
        }
    }

}
