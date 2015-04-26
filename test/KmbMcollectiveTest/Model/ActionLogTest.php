<?php
namespace KmbMcollectiveTest\Model;

use KmbMcollective\Model\ActionLog;
use KmbMcollective\Model\CommandLog;
use KmbMcollective\Model\CommandReply;
use KmbMcollectiveTest\Bootstrap;

class ActionLogTest extends \PHPUnit_Framework_TestCase
{
    /** @var ActionLog */
    protected $action;

    protected function setUp()
    {
        $this->action = new ActionLog('d8e8fca2dc0f896fd7cb4cb0031ba249');
    }

    /** @test */
    public function canCreateAction()
    {
        $this->assertInstanceOf('KmbMcollective\Model\ActionLog',$this->action);
    }

    /** @test */
    public function canSetCommands()
    {
        $this->action->setCommands([ new CommandLog('d3b07384d113edec49eaa6238ad5ff00')]);
        $this->assertContainsOnlyInstancesOf('KmbMcollective\Model\CommandLog',$this->action->getCommands());
    }
    /** @test */
    public function canAddCommand(){
        $this->action->addCommand(new CommandLog('d3b07384d113edec49eaa6238ad5ff00'));
        $this->action->addCommand(new CommandLog('d3b07384d113edec49eaa6238ad5ff00'));
        $this->assertEquals(2, sizeof( $this->action->getCommands() ) );
    }

    /** @test */
    public function canTestIfHasCommands(){
        $this->action->setCommands([new CommandLog('foo'), new CommandLog('bar')]);
        $this->assertEquals(true, $this->action->hasCommands());
    }

    /** @test */
    public function canDetectNoCommands(){
        $this->assertEquals(false, $this->action->hasCommands());
    }

    /** @test */
    public function canGetByRequestId(){
        $command = new CommandLog('1234');
        $reply = new CommandReply();
        $reply->setRequestId('1234');
        $command->addReply($reply);

        $command2 = new CommandLog('5678');
        $reply2 = new CommandReply();
        $reply2->setRequestId('5678');
        $command2->addReply($reply2);

        $this->action->addCommand($command);
        $this->action->addCommand($command2);

        $this->assertEquals($command2, $this->action->getByRequestId('5678'));

    }

    /** @test */
    public function canGetServerCount(){
        $command = new CommandLog('1234');
        $reply = new CommandReply();
        $reply->setRequestId('1234');
        $reply->setHostname('foo1');
        $command->addReply($reply);

        $command2 = new CommandLog('5678');
        $reply2 = new CommandReply();
        $reply2->setRequestId('5678');
        $reply2->setHostname('foo2');
        $reply3 = new CommandReply();
        $reply3->setRequestId('5678');
        $reply3->setHostname('foo1');
        $command2->addReply($reply2);
        $command2->addReply($reply3);

        $this->action->addCommand($command);
        $this->action->addCommand($command2);

        $this->assertEquals(2, $this->action->getServerReplyCount());
    }

    /** @test */
    public function canGetGlobalStatus(){
        $command = new CommandLog('1234');
        $reply = new CommandReply();
        $reply->setRequestId('1234');
        $reply->setHostname('foo1');
        $reply->setStatusCode(5);
        $command->addReply($reply);

        $command2 = new CommandLog('5678');
        $reply2 = new CommandReply();
        $reply2->setRequestId('5678');
        $reply2->setHostname('foo2');
        $reply2->setStatusCode(0);
        $reply3 = new CommandReply();
        $reply3->setRequestId('5678');
        $reply3->setHostname('foo1');
        $reply3->setStatusCode(0);
        $command2->addReply($reply2);
        $command2->addReply($reply3);

        $this->action->addCommand($command);
        $this->action->addCommand($command2);

        $stat = $this->action->getGlobalStatus();

        $this->assertEquals('partial',$stat['status']);
        $this->assertEquals(1,$stat['errors']);
    }

    /** @test */
    public function canGetResultsByHost(){
        $command = new CommandLog('1234');
        $reply = new CommandReply();
        $reply->setRequestId('1234');
        $reply->setHostname('foo1');
        $reply->setStatusCode(5);
        $command->addReply($reply);

        $command2 = new CommandLog('5678');
        $reply2 = new CommandReply();
        $reply2->setRequestId('5678');
        $reply2->setHostname('foo2');
        $reply2->setStatusCode(0);
        $reply3 = new CommandReply();
        $reply3->setRequestId('5678');
        $reply3->setHostname('foo1');
        $reply3->setStatusCode(0);
        $command2->addReply($reply2);
        $command2->addReply($reply3);

        $this->action->addCommand($command);
        $this->action->addCommand($command2);


        $this->assertEquals(['foo1' => [$reply->toArray(),$reply3->toArray()],'foo2' => [$reply2->toArray()]],$this->action->getResultByHost());
    }

}
