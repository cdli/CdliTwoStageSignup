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
     * @var TestCaseOptions
     */
    protected static $options;

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

    public static function setOptions(TestCaseOptions $o)
    {
        self::$options = $o;
    }

    public function getOptions()
    {
        return self::$options;
    }
}
