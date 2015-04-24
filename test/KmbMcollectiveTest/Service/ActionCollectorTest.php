<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Service\ActionCollector;
use KmbMcollectiveTest\Bootstrap;

use KmbMcollectiveTest\DatabaseInitTrait;
use Zend\Db\Adapter\AdapterInterface;

class ActionCollectorTest extends \PHPUnit_Framework_TestCase
{


    use DatabaseInitTrait;
    /** @var NodeCollector */
    protected $actionCollector;
    protected static $actionLogRepository;
    protected static $connection;

    public static function setUpBeforeClass()
    {
        $serviceManager = Bootstrap::getServiceManager();
        static::$actionLogRepository = $serviceManager->get('ActionLogRepository');
                /** @var $dbAdapter AdapterInterface */
        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        static::$connection = $dbAdapter->getDriver()->getConnection()->getResource();

        static::initSchema(static::$connection);

    }
    protected function setUp()
    {
        $this->actionCollector = new ActionCollector();
        $this->actionCollector->setActionLogRepository(static::$actionLogRepository);
        static::initFixtures(static::$connection);
    }

    /** @test */
    public function canFindAll()
    {
        $collection = $this->actionCollector->findAll([
            'start' => 0,
            'length' => 2,
        ]);

        $this->assertInstanceOf('GtnDataTables\Model\Collection', $collection);
        $this->assertEquals(2, count($collection->getData()));
        $this->assertEquals(2, $collection->getTotal());
        $this->assertEquals(3, $collection->getFilteredCount());
    }

}
