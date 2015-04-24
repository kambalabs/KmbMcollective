<?php
namespace KmbMcollectiveTest\Model;

use KmbMcollective\Model\CommandLog;
use KmbMcollective\Model\CommandReply;
use KmbMcollectiveTest\Bootstrap;

class CommandLogTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommandLog */
    protected $cmd;

    protected function setUp()
    {
        $this->cmd = new CommandLog('d8e8fca2dc0f896fd7cb4cb0031ba249');
    }

    /** @test */
    public function canAddDiscoveredNode(){
        $this->cmd->setNodes(['foo.bar.com', 'bar.bar.de']);
        $this->assertEquals(2,sizeof($this->cmd->getNodes()));
    }

    /** @test */
    public function canSetReplies(){
        $this->cmd->setReplies([new CommandReply(1), new CommandReply(2)]);
        $this->assertEquals(2, sizeof($this->cmd->getReplies()));
    }

    /** @test */
    public function canAddReply(){
        $this->cmd->setReplies([new CommandReply(1), new CommandReply(2)]);
        $this->cmd->addReply(new CommandReply(3));
        $this->assertEquals(3,sizeof($this->cmd->getReplies()));
    }

    /** @test */
    public function canTellIfItHasReplies(){
        $this->cmd->setReplies([new CommandReply(1), new CommandReply(2)]);
        $this->assertTrue($this->cmd->hasReplies());
    }

    /** @test */
    public function canGetFinishedReplies(){
        $finishedReply = new CommandReply(3);
        $finishedReply->finish(true);
        $this->cmd->setReplies([new CommandReply(1), new CommandReply(2), $finishedReply]);
        $this->assertEquals([$finishedReply], $this->cmd->getAllFinishedReplies());
    }
}
