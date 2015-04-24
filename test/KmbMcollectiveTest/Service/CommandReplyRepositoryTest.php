<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Model\CommandReply;
use KmbMcollective\Service\CommandReplyRepository;
use KmbMcollectiveTest\Bootstrap;
use KmbMcollectiveTest\DatabaseInitTrait;
use Zend\Db\Adapter\AdapterInterface;

class CommandReplyRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseInitTrait;

    /** @var \PDO */
    protected static $connection;

    /** @var CommandReplyRepository */
    protected static $repository;

    public static function setUpBeforeClass()
    {
        $serviceManager = Bootstrap::getServiceManager();
        static::$repository = $serviceManager->get('CommandReplyRepository');

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
    public function canAdd(){
        $cmdreply = new CommandReply();
        static::$repository->add($cmdreply);
        $this->assertEquals(6,$cmdreply->getId());
    }

    /** @test */
    public function canUpdate(){
        $cmdr = static::$repository->getById(1);
        $cmdr->setHostname('coin.coin.coin');
        static::$repository->update($cmdr);
        $cmdnew = static::$repository->getById(1);
        $this->assertEquals('coin.coin.coin',$cmdnew->getHostname());
    }

    /** @test */
    public function canGetById(){
        $cmdr = static::$repository->getById(1);
        $this->assertInstanceOf('KmbMcollective\Model\CommandReply',$cmdr);
        $this->assertEquals(1,$cmdr->getId());
    }

    /** @test */
    public function canGetAllByRequestId(){
        $cmdr = static::$repository->getAllByRequestId('239397e749bf02940b36ad80c721cdfa');
        $this->assertContainsOnlyInstancesOf('KmbMcollective\Model\CommandReply',$cmdr);
        $this->assertEquals(2,count($cmdr));

    }
}
