<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Service\ActionLogRepository;
use KmbMcollectiveTest\Bootstrap;

class ActionLogRepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function canCreateService()
    {
        /** @var ActionLogRepository $service */
        $service = Bootstrap::getServiceManager()->get('ActionLogRepository');
        $this->assertInstanceOf('KmbMcollective\Service\ActionLogRepository', $service);
        $this->assertEquals('action_logs', $service->getTableName());
        $this->assertInstanceOf('KmbMcollective\Service\CommandLogRepository', $service->getCommandLogRepository());

    }
}
