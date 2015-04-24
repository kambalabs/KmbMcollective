<?php
namespace KmbMcollectiveTest\Service;


use KmbMcollectiveTest\Bootstrap;
use KmbMcollective\Service\ActionCollectorFactory;

class ActionCollectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function canCreateService()
    {

        $factory = new ActionCollectorFactory();
        $service = $factory->createService(Bootstrap::getServiceManager());

        $this->assertInstanceOf('KmbMcollective\Service\ActionCollector', $service);
        $this->assertInstanceOf('KmbMcollective\Service\ActionLogRepository',$service->getActionLogRepository());

    }
}
