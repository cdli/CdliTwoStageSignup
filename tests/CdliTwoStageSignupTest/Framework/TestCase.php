<?php

namespace CdliTwoStageSignupTest\Framework;

use PHPUnit_Framework_TestCase;
use Zend\Di\DependencyInjectionInterface as Locator;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Locator
     */
    protected static $locator;

    /**
     * @param Locator $locator
     */
    public static function setLocator(Locator $locator)
    {
        self::$locator = $locator;
    }

    /**
     * @return Locator
     */
    public function getLocator()
    {
    	return self::$locator;
    }
}
