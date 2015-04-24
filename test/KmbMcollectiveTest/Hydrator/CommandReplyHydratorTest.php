<?php
namespace KmbMcollectiveTest\Hydrator;

use KmbMcollective\Model\CommandReply;
use KmbMcollective\Hydrator\CommandReplyHydrator;

class CommandReplyHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function canHydrate()
    {
        $repl = new CommandReply();
        $hydrator = new CommandReplyHydrator();
        $obj = $hydrator->hydrate(['id' => 2, 'hostname' => 'foo.bar.coin', 'username' => 'ABCD1234', 'requestid' => '14758f1afd44c09b7992073ccf00b43d', 'agent' => 'do', 'action' => 'things', 'result' => 'OK', 'statuscode' => 2, 'finished' => true], $repl);

        $this->assertInstanceOf('KmbMcollective\Model\CommandReply',$obj);
        $this->assertEquals(2, $repl->getId());
        $this->assertEquals('foo.bar.coin', $repl->getHostname());
        $this->assertEquals('ABCD1234', $repl->getUser());
        $this->assertEquals('14758f1afd44c09b7992073ccf00b43d',$repl->getRequestId());
        $this->assertEquals('do', $repl->getAgent());
        $this->assertEquals('things',$repl->getAction());
        $this->assertEquals('OK',$repl->getResult());
        $this->assertEquals(2, $repl->getStatusCode());
        $this->assertTrue($repl->is_finished());
    }

    /** @test */
    public function canExtract(){
        $hydrator = new CommandReplyHydrator();
        $repl = new CommandReply(1);
        $repl->setAction('things');
        $repl->setAgent('do');
        $repl->setRequestId('14758f1afd44c09b7992073ccf00b43d');
        $repl->setUser('ABCD1234');
        $repl->setHostname('foo.bar.coin');
        $repl->setStatusCode(0);
        $repl->setResult('{fooo}');
        $repl->finish(true);

        $this->assertEquals(['id' => 1, 'hostname' => 'foo.bar.coin', 'username' => 'ABCD1234', 'requestid' => '14758f1afd44c09b7992073ccf00b43d', 'agent' => 'do', 'action' => 'things', 'statuscode' => 0, 'result' => '{fooo}', 'finished' => true], $hydrator->extract($repl));
    }
}
