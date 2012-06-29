<?php
namespace CdliTwoStageSignupTest\Framework;

use Zend\Stdlib\AbstractOptions;

class TestCaseOptions extends AbstractOptions
{
    protected $databaseSchemaUp;
    protected $databaseSchemaDown;

    public function setDatabaseSchemaUp($schema)
    {
        $this->databaseSchemaUp = $schema;
    }

    public function getDatabaseSchemaUp()
    {
        return $this->databaseSchemaUp;
    }

    public function setDatabaseSchemaDown($schema)
    {
        $this->databaseSchemaDown = $schema;
    }

    public function getDatabaseSchemaDown()
    {
        return $this->databaseSchemaDown;
    }
}
