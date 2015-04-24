<?php
namespace KmbMcollectiveTest\Hydrator;

use KmbMcollective\Model\ActionLog;
use KmbMcollective\Hydrator\ActionLogHydrator;

class ActionLogHydratorTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function canHydrate()
    {
        $log = new ActionLog();
        $hydrator = new ActionLogHydrator();
        $obj = $hydrator->hydrate(['actionid' => 'eca4a144446d853f94b6bbdfe6cfbd76', 'environment' => 'FOO_STABLE', 'parameters' => '{"foo": "bar"}', 'description' => 'test action','login' => 'ABCD1234', 'fullname' => 'AB CD','created_at' => time(), 'finished'=>false ],$log);
        $this->assertInstanceOf('KmbMcollective\Model\ActionLog',$obj);
        $this->assertEquals('eca4a144446d853f94b6bbdfe6cfbd76', $obj->getId());
        $this->assertEquals('FOO_STABLE',$obj->getEnvironment());
        $this->assertEquals('{"foo": "bar"}',$obj->getParameters());
        $this->assertEquals('test action',$obj->getDescription());
        $this->assertEquals('ABCD1234',$obj->getLogin());
        $this->assertEquals('AB CD',$obj->getFullName());
        $this->assertFalse($obj->is_finished());
    }

    /** @test */
    public function canExtract(){
        $log = new ActionLog();
        $hydrator = new ActionLogHydrator();
        $obj = $hydrator->hydrate(['actionid' => 'eca4a144446d853f94b6bbdfe6cfbd76', 'environment' => 'FOO_STABLE', 'parameters' => '{"foo": "bar"}', 'description' => 'test action','login' => 'ABCD1234', 'fullname' => 'AB CD','created_at' => date('Y-m-d H:i:s'), 'finished'=>false ],$log);
        $this->assertEquals(['actionid' => 'eca4a144446d853f94b6bbdfe6cfbd76', 'environment' => 'FOO_STABLE', 'parameters' => '{"foo": "bar"}', 'description' => 'test action','login' => 'ABCD1234', 'fullname' => 'AB CD','created_at' => date('Y-m-d H:i:s'), 'finished'=>false ], $hydrator->extract($obj));
    }

}
