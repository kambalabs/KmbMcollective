<?php
namespace KmbMcollectiveTest\Model;

use KmbMcollective\Model\CommandReply;
use KmbMcollectiveTest\Bootstrap;

class CommandReplyTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommandReply */
    protected $cmdRep;

    protected function setUp()
    {
        $this->cmd = new CommandReply(1);
    }

    /** @test */
    public function canCreateReply(){
        $this->assertInstanceOf('KmbMcollective\Model\CommandReply',$this->cmd);
    }

}
