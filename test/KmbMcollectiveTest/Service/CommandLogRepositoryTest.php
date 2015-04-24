<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Model\CommandReply;
use KmbMcollective\Model\CommandLog;
use KmbMcollectiveTest\Bootstrap;
use KmbMcollectiveTest\DatabaseInitTrait;
use Zend\Db\Adapter\AdapterInterface;

class CommandLogRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseInitTrait;

    /** @var \PDO */
    protected static $connection;

    /** @var CommandReplyRepository */
    protected static $repository;

    public static function setUpBeforeClass()
    {
        $serviceManager = Bootstrap::getServiceManager();
        static::$repository = $serviceManager->get('CommandLogRepository');

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
        $cmdreply = new CommandLog('fd6b3c655b8d3bfa09833fe5eebb7a11');
        static::$repository->add($cmdreply);
        $this->assertEquals('fd6b3c655b8d3bfa09833fe5eebb7a11',$cmdreply->getId());
    }

    /** @test */
    public function canAddWithReplies(){
        $cmd = new CommandLog('deadbeef');
        $reply1 = new CommandReply();
        $reply1->setRequestId('deadbeef');
        $reply2 = new CommandReply();
        $reply2->setRequestId('deadbeef');
        $cmd->setReplies([$reply1, $reply2]);
        static::$repository->add($cmd);
        $this->assertEquals(1, static::$connection->query("SELECT count(*) FROM command_logs WHERE requestid = 'deadbeef'")->fetchColumn(0));
        $this->assertEquals(2, static::$connection->query("SELECT count(*) FROM command_reply_logs WHERE requestid = 'deadbeef'")->fetchColumn(0));
    }

    /** @test */
    public function canGetById(){
        $cmdl = static::$repository->getById('239397e749bf02940b36ad80c721cdfa');
        $this->assertInstanceOf('KmbMcollective\Model\CommandLog',$cmdl);
        $this->assertEquals('239397e749bf02940b36ad80c721cdfa',$cmdl->getId());
    }

    /** @test */
    public function canGetReply(){
        $cmdl = static::$repository->getById('239397e749bf02940b36ad80c721cdfa');
        $this->assertEquals(2, sizeof($cmdl->getReplies()));
    }

    /** @test */
    public function canGetReplyForAHost(){
        $cmdl = static::$repository->getById('239397e749bf02940b36ad80c721cdfa');
        $reply = $cmdl->getReplyFor('foo.bar.coin');
        $this->assertInstanceOf('KmbMcollective\Model\CommandReply',$reply);
        $this->assertEquals('foo.bar.coin',$reply->getHostname());
    }

}
