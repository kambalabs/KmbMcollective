<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Model\McollectiveHistory;
use KmbMcollective\Service\McollectiveHistoryRepository;
use KmbMcollectiveTest\Bootstrap;
use KmbMcollectiveTest\DatabaseInitTrait;
use Zend\Db\Adapter\AdapterInterface;

class McollectiveHistoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseInitTrait;

    /** @var \PDO */
    protected static $connection;

    /** @var McollectiveHistoryRepository */
    protected static $repository;

    public static function setUpBeforeClass()
    {
        $serviceManager = Bootstrap::getServiceManager();
        static::$repository = $serviceManager->get('McollectiveHistoryRepository');

        /** @var $dbAdapter AdapterInterface */
        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        static::$connection = $dbAdapter->getDriver()->getConnection()->getResource();

        static::initSchema(static::$connection);
    }

    protected function setUp()
    {
        static::initFixtures(static::$connection);
    }

    /** @test */
    public function canGetFilteredLogs()
    {
        $logs = static::$repository->getFilteredLogs();

        $this->assertEquals(3, count($logs));
        /** @var McollectiveHistory $firstLog */
        $firstLog = $logs[0];
        $this->assertInstanceOf('KmbMcollective\Model\McollectiveHistory', $firstLog);
        $this->assertEquals('df', $firstLog->getAction());
    }

    public function canLimitFilterlogs()
    {
        $logs = static::$repository->getFilteredLogs(null,null,1);
        $this->assertEquals(1, count($logs));
    }
}
