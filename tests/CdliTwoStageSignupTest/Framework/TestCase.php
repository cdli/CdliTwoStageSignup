<?php

namespace CdliTwoStageSignupTest\Framework;

use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected static $locator;

    /**
     * @param ServiceLocatorInterface $locator
     */
    public static function setServiceLocator(ServiceLocatorInterface $locator)
    {
        self::$locator = $locator;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
    	return self::$locator;
    }
}
