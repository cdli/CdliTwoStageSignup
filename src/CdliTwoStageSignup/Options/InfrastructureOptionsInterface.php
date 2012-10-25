<?php
namespace CdliTwoStageSignup\Options;

use Zend\Stdlib\AbstractOptions;

interface InfrastructureOptionsInterface
{
    public function setStorageAdapter($adapter);
}
