<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Service\McollectiveHistoryRepository;
use KmbMcollectiveTest\Bootstrap;

class McollectiveHistoryRepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function canCreateService()
    {
        /** @var McollectiveHistoryRepository $service */
        $service = Bootstrap::getServiceManager()->get('McollectiveHistoryRepository');

        $this->assertInstanceOf('KmbMcollective\Service\McollectiveHistoryRepository', $service);
        $this->assertEquals('mcollective_logs', $service->getLogTableName());
        $this->assertEquals('KmbMcollective\Model\McollectiveLog', $service->getLogClass());
        $this->assertInstanceOf('KmbMcollective\Hydrator\McollectiveLogHydrator', $service->getLogHydrator());
        $this->assertEquals('mcollective_log_id_seq', $service->getLogTableSequenceName());
    }
}
