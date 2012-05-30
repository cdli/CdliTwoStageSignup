<?php

namespace CdliTwoStageSignupTest\Framework;

use Zend\Db\Adapter\Adapter;
use CdliTwoStageSignup\Module as modCTSS;

class MapperTestCase extends TestCase
{

    public function setUp()
    {
        $this->db = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $this->dbSchemaDown();
        $this->dbSchemaUp();
    }

    protected function dbSchemaDown()
    {
        $this->importSchema(__DIR__ . '/../../../data/' . modCTSS::getOption('test_database_schema_down'));
    }

    protected function dbSchemaUp()
    {
        $this->importSchema(__DIR__ . '/../../../data/' . modCTSS::getOption('test_database_schema_up'));
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
