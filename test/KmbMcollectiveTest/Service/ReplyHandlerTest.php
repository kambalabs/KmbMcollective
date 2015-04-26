<?php
namespace KmbMcollectiveTest\Service;

use KmbMcollective\Model\ActionLog;
use KmbMcollective\Model\CommandLog;
use KmbMcollectiveTest\Bootstrap;
use KmbMcollectiveTest\DatabaseInitTrait;
use Zend\Db\Adapter\AdapterInterface;

class ReplyHandlerTest extends \PHPUnit_Framework_TestCase
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
    public function canProcess(){
        $serviceManager = Bootstrap::getServiceManager();
        $handler = $serviceManager->get('ReplyHandler');
        $log = json_decode('{"actionid":"cc73406c25bdecba6c8d4cccedc233f1","requestid":"132f4a6ecc8b5667b72e70c5b9f0d28b","senderid":"mco01.puppet.mbs","senderagent":"admin","senderaction":"df","caller":"NXLM5803","hostname":"puppet-batch01-prod.admin.cloud.mbs","type":"default"}');
        $handler->newprocess($log);
        $action = $handler->getActionLogRepository()->getById("132f4a6ecc8b5667b72e70c5b9f0d28b");
        $command = $handler->getCommandLogRepository()->getById('132f4a6ecc8b5667b72e70c5b9f0d28b');
        $reply = $command->getReplies();
        $this->assertInstanceOf('KmbMcollective\Model\CommandLog',$command);
        $this->assertInstanceOf('KmbMcollective\Model\ActionLog',$action);
        $this->assertEquals('CLI command from mco01.puppet.mbs',$handler->getActionLogRepository()->getById("132f4a6ecc8b5667b72e70c5b9f0d28b")->getDescription());
        $this->assertEquals(1,sizeof($reply));
        $this->assertEquals('admin',$reply[0]->getAgent());
        $this->assertEquals('df',$reply[0]->getAction());


        $this->assertNull($handler->getCommandLogRepository()->getById("132f4a6ecc8b5667b72e70c5b9f0d28b")->getReplies()[0]->getStatusCode());
        $log = json_decode('{"requestid":"132f4a6ecc8b5667b72e70c5b9f0d28b","hostname":"puppet-batch01-prod.admin.cloud.mbs","agent":"admin","statuscode":0,"data":"{\"status\":0,\"out\":\"Filesystem      Size  Used Avail Use% Mounted on\\nrootfs                    5.9G  2.8G  2.8G  50% /\\nudev                       10M     0   10M   0% /dev\\ntmpfs                     397M  208K  397M   1% /run\\n/dev/mapper/ROOT-ROOT     5.9G  2.8G  2.8G  50% /\\ntmpfs                     5.0M     0  5.0M   0% /run/lock\\ntmpfs                     794M     0  794M   0% /run/shm\\n/dev/sda1                 249M   31M  206M  13% /boot\\n/dev/mapper/ROOT-VAR_LOG  2.3G   70M  2.2G   4% /var/log\\n\",\"err\":\"\"}"}');
        $handler->newprocess($log);
        $this->assertEquals(0,$handler->getCommandLogRepository()->getById("132f4a6ecc8b5667b72e70c5b9f0d28b")->getReplies()[0]->getStatusCode());
        // With SQLite true = 1
        $this->assertEquals(1,$handler->getCommandLogRepository()->getById("132f4a6ecc8b5667b72e70c5b9f0d28b")->getReplies()[0]->is_finished());
    }

    /** @test */
    public function canProcessWithError(){
        $serviceManager = Bootstrap::getServiceManager();
        $actionRepo = $serviceManager->get('ActionLogRepository');
        $commandRepo = $serviceManager->get('CommandLogRepository');

        $action = new ActionLog('8535f1d716bb1fd87bc54939cf518df5');
        $command = new CommandLog('42fe2ce966a05f18a861708c35808f9f');
        $action->addCommand($command);

        $actionRepo->add($action);

        $handler = $serviceManager->get('ReplyHandler');
        $log = json_decode('{"requestid":"42fe2ce966a05f18a861708c35808f9f","hostname":"slides.sys.op.mbs","agent":"cmd","statuscode":5,"data":"[Action id: 8535f1d716bb1fd87bc54939cf518df5] Access Denied for serveur to cmd::go"}');
        $handler->newprocess($log);

        $updatedAction = $actionRepo->getById('8535f1d716bb1fd87bc54939cf518df5');
        $this->assertEquals(5,$updatedAction->getCommands()[0]->getReplies()[0]->getStatusCode());

    }

}
