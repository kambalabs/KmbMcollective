<?php
/**
 * @copyright Copyright (c) 2014 Orange Applications for Business
 * @link      http://github.com/multimediabs/kamba for the canonical source repository
 *
 * This file is part of kamba.
 *
 * kamba is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * kamba is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with kamba.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace KmbMcollective\Service;

use GtnPersistZendDb\Infrastructure\ZendDb\Repository;
use GtnPersistBase\Model\AggregateRootInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use KmbMcollective\Models\CommandLog;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;



class ActionLogRepository extends Repository
{
    protected $commandLogTableName;
    protected $commandLogSequenceName;
    protected $commandLogRepository;
    protected $commandLogHydrator;
    protected $commandLogClass;


    public function add(AggregateRootInterface $aggregateRoot)
    {
        parent::add($aggregateRoot);

        if ($aggregateRoot->hasCommands()) {
            foreach ($aggregateRoot->getCommands() as $cmd) {
                $this->commandLogRepository->add($cmd);
            }
        }
        return $this;
    }


    /**
     * @return Select
     */
    protected function getSelect()
    {
        $select = parent::getSelect()
            ->join(
                ['c' => $this->commandLogRepository->getTableName()],
                $this->getTableName() . '.'.$this->getTableId() .' = c.actionid',
                [
                    'c.requestid' => 'requestid',
                    'c.actionid' => 'actionid',
                ],
                Select::JOIN_LEFT
            )
            ->join(
                ['r' => $this->commandLogRepository->getReplyRepository()->getTableName()],
                'c.requestid = r.requestid',
                [
                    'r.requestid' => 'requestid',
                    'r.id' => 'id',
                    'r.username' => 'username',
                    'r.hostname' => 'hostname',
                    'r.statuscode' => 'statuscode',
                    'r.result' => 'result',
                    'r.agent' => 'agent',
                    'r.action' => 'action',
                    'r.finished' => 'finished'
                ],
                Select::JOIN_LEFT
            )
            ;
        return $select;
    }


    public function getAllByIds($ids, $order = 'created_at'){
        $select = $this->getSelect();
        $select->where([$this->tableName.'.actionid'=>$ids]);
        $select->order( $order != "" ? $order : 'created_at DESC');
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));

    }

    public function getFiltered($query,$offset,$limit,$order = 'created_at'){
        $actionids = $this->getActionIdWith($query,$offset,$limit,$order);
        if(empty($actionids)){
            return [];
        }else{
            return $this->getAllByIds($actionids,$order);
        }
    }

    public function getActionIdWith($query,$offset,$limit,$order) {
        $select = $this->getSelect();
        if($query != ""){
            $where = new Where();
            $where->nest
                ->like('r.username','%'.$query.'%')
                ->or
                ->like('fullname','%'.$query.'%')
                ->or
                ->like('r.hostname','%'.$query.'%')
                ->or
                ->like('description','%'.$query.'%')
                ->unnest;
            $select->where($where);
        }
        $select->order((isset($order) && $order != "") ? $order : 'created_at');

        $sub = new Select();
        $sub->from(['f' => $select]);
        $sub->columns([new Expression('Distinct actionid,created_at')]);
        if(isset($limit)){
            $sub->offset($offset)->limit($limit);
        }


        $result = [];
        foreach($this->performRead($sub) as $row){
            $result[] = $row['actionid'];
        }
        return $result;
    }

    public function getCountFor($query){
        return count($this->getFiltered($query,null,null,null));
    }

    /**
     * @param ResultInterface $result
     * @return array
     */
    protected function hydrateAggregateRootsFromResult(ResultInterface $result)
    {
        $aggregateRootClassName = $this->getAggregateRootClass();
        $cmdLogClassName = $this->commandLogRepository->getAggregateRootClass();
        $cmdReplyClassName = $this->commandLogRepository->getReplyClass();
        $aggregateRoots = [];
        foreach ($result as $row) {
            $actionid = $row['actionid'];
            /** @var CommandLogInterface $aggregateRoot */
            if (!array_key_exists($actionid, $aggregateRoots)) {
                $aggregateRoot = new $aggregateRootClassName;
                $this->aggregateRootHydrator->hydrate($row, $aggregateRoot);
                $aggregateRoots[$actionid] = $aggregateRoot;
            } else {
                $aggregateRoot = $aggregateRoots[$actionid];
            }
            if (isset($row['c.requestid'])) {
                $command = $aggregateRoot->getCommandById($row['c.requestid']);
                if(! $command)
                {
                    $command = new $cmdLogClassName;
                    $this->commandLogRepository->getAggregateRootHydrator()->hydrate($row,$command);
                    $aggregateRoot->addCommand($command);
                }
                if (isset($row['r.requestid'])) {
                    $reply = new $cmdReplyClassName;
                    $this->commandLogRepository->getReplyHydrator()->hydrate($row,$reply);
                    $command->addReply($reply);
                }
            }
        }
        return array_values($aggregateRoots);
    }





    public function setCommandLogTable($logtable)
    {
        $this->commandLogTableName = $logtable;
        return $this;
    }

    public function getCommandLogTableName()
    {
        return $this->commandLogTableName;
    }

    public function setCommandLogSequenceName($sequence)
    {
        $this->commandLogSequenceName = $sequence;
        return $this;
    }

    public function getCommandLogSequenceName()
    {
        return $this->commandLogSequenceName;
    }

    public function setCommandLogRepository($repository)
    {
        $this->commandLogRepository = $repository;
        return $this;
    }

    public function getCommandLogRepository()
    {
        return $this->commandLogRepository;
    }

    public function setCommandLogHydrator($hydrator)
    {
        $this->commandLogHydrator = $hydrator;
        return $this;
    }

    public function getCommandLogHydrator()
    {
        return $this->commandLogHydrator;
    }

    public function setCommandLogClass($class)
    {
        $this->commandLogClass = $class;
        return $this;
    }
    public function getCommandLogClass()
    {
        return $this->commandLogClass;
    }

}
