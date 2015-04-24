<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Service\ReplyHandler;
use KmbMcollectiveTest\Bootstrap;

class ReplyHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function canCreateService()
    {
        /** @var ReplyHandler $service */
        $service = Bootstrap::getServiceManager()->get('ReplyHandler');
        $this->assertInstanceOf('KmbMcollective\Service\ReplyHandler', $service);
        $this->assertInstanceOf('KmbMcollective\Service\ActionLogRepository', $service->getActionLogRepository());
        $this->assertInstanceOf('KmbMcollective\Service\CommandLogRepository', $service->getCommandLogRepository());

    }
}
