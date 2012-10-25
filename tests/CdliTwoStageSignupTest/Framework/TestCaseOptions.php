<?php
namespace CdliTwoStageSignupTest\Framework;

use Zend\Stdlib\AbstractOptions;

class TestCaseOptions extends AbstractOptions
{
    protected $databaseSchemaUp;
    protected $databaseSchemaDown;
    protected $enableZendDbTests;
    protected $enableDoctrineOrmTests;

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

    public function setEnableZendDbTests($tf)
    {
        $this->enableZendDbTests = ($tf == true);
        return $this;
    }

    public function getEnableZendDbTests()
    {
        return $this->enableZendDbTests;
    }

    public function setEnableDoctrineOrmTests($tf)
    {
        $this->enableDoctrineOrmTests = ($tf == true);
        return $this;
    }

    public function getEnableDoctrineOrmTests()
    {
        return $this->enableDoctrineOrmTests;
    }
}
