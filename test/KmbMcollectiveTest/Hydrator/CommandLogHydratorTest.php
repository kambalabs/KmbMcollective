<?php
namespace KmbMcollectiveTest\Hydrator;

use KmbMcollective\Model\CommandLog;
use KmbMcollective\Hydrator\CommandLogHydrator;

class CommandLogHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function canHydrate()
    {
        $log = new CommandLog();
        $hydrator = new CommandLogHydrator();
        $obj = $hydrator->hydrate(['requestid' => '74253c5958edce36a939b9ef7f3f4452', 'actionid' => 'deadbeef'],$log);
        $this->assertInstanceOf('KmbMcollective\Model\CommandLog',$obj);
        $this->assertEquals('74253c5958edce36a939b9ef7f3f4452', $obj->getId());
        $this->assertEquals('deadbeef', $obj->getActionId());
    }

    /** @test */
    public function canExtract(){
        $hydrator = new CommandLogHydrator();
        $log = new CommandLog();
        $log->setId('74253c5958edce36a939b9ef7f3f4452');
        $log->setActionId('deadbeef');
        $this->assertEquals(['requestid' => '74253c5958edce36a939b9ef7f3f4452', 'actionid' => 'deadbeef'], $hydrator->extract($log));
    }

}
