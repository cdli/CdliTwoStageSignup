<?php
namespace CdliTwoStageSignupTest;

class ServiceConfigurationTest extends Framework\TestCase
{
    public function testModuleOptions()
    {
        $options = $this->getServiceLocator()->get('cdlitwostagesignup_module_options');
        $this->assertInstanceOf('CdliTwoStageSignup\Options\ModuleOptions', $options);
    }
}
