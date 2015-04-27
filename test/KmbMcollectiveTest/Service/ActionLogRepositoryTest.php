<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Model\ActionLog;
use KmbMcollective\Model\CommandLog;
use KmbMcollectiveTest\Bootstrap;
use KmbMcollectiveTest\DatabaseInitTrait;
use Zend\Db\Adapter\AdapterInterface;

class ActionLogRepositoryTest extends \PHPUnit_Framework_TestCase
{

    use DatabaseInitTrait;

    /** @var \PDO */
    protected static $connection;

    /** @var ActionLogRepository */
    protected static $repository;

    public static function setUpBeforeClass()
    {
        $serviceManager = Bootstrap::getServiceManager();
        static::$repository = $serviceManager->get('ActionLogRepository');

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
        $alog = new ActionLog('fd6b3c655b8d3bfa09833fe5eebb7a11');
        static::$repository->add($alog);
        $this->assertEquals('fd6b3c655b8d3bfa09833fe5eebb7a11',$alog->getId());
        $this->assertNotNull($alog->getCreationDate());
        $this->assertNotNull($alog->is_finished());
    }

    /** @test */
    public function canUpdate(){
        $alog = static::$repository->getById('b1ddad6cb8233287f8087dc36074b80a');
        $command = new CommandLog('deadbeef');
        $alog->addCommand($command);
        static::$repository->update($alog);

        $newlog = static::$repository->getById('b1ddad6cb8233287f8087dc36074b80a');
        $this->assertEquals(2, count($newlog->getCommands()));

    }



    /** @test */
    public function canAddWithCommands(){
        $alog = new ActionLog('1234');
        $cmd = new CommandLog('5678');
        $alog->addCommand($cmd);
        static::$repository->add($alog);
        $this->assertEquals(1, static::$connection->query("SELECT count(*) FROM action_logs WHERE actionid = '1234'")->fetchColumn(0));
        $this->assertEquals(1, static::$connection->query("SELECT count(*) FROM command_logs WHERE actionid = '1234'")->fetchColumn(0));
    }

    /** @test */
    public function canGetById(){
        $cmdl = static::$repository->getById('80cb13ec244531e6ccc5203a1d57a56e');
        $this->assertInstanceOf('KmbMcollective\Model\ActionLog',$cmdl);
        $this->assertEquals('80cb13ec244531e6ccc5203a1d57a56e',$cmdl->getId());
        $this->assertEquals(1,sizeof($cmdl->getCommands()));
    }

    /** @test */
    public function canGetAllByIds(){
        $cmda = static::$repository->getAllByIds(['1099df8aee3e341d807f19ecbcd07ff0','b1ddad6cb8233287f8087dc36074b80a']);
        $this->assertEquals(2,sizeof($cmda));
        $this->assertContainsOnlyInstancesOf('KmbMcollective\Model\ActionLog',$cmda);
        $this->assertTrue(true);
    }

    /** @test */
    public function canGetActionIdfromConstraints(){
        $cmdaction = static::$repository->getActionIdWith('defg', 0,1,null);
        $this->assertEquals(1,sizeof($cmdaction));
        $this->assertEquals(['1099df8aee3e341d807f19ecbcd07ff0'],$cmdaction);

    }


}
