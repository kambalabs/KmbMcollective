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


class CommandLogRepository extends Repository
{
    protected $reply_class;
    protected $reply_hydrator;
    protected $reply_table;
    protected $reply_table_seq;
    protected $reply_repository;



    public function add(AggregateRootInterface $aggregateRoot)
    {
        parent::add($aggregateRoot);

        if ($aggregateRoot->hasReplies()) {
            foreach ($aggregateRoot->getReplies() as $reply) {
                $this->reply_repository->add($reply);
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
                ['r' => $this->reply_repository->getTableName()],
                $this->getTableName() . '.'.$this->getTableId() .' = r.requestid',
                [
                    'r.requestid' => 'requestid',
                    'r.id' => 'id',
                    'r.username' => 'username',
                    'r.hostname' => 'hostname',
                    'r.statuscode' => 'statuscode',
                    'r.result' => 'result',
                    'r.agent' => 'agent',
                    'r.action' => 'action',
                    'r.finished' => 'finished',
                ],
                Select::JOIN_LEFT
            );
        return $select;
    }

    /**
     * @param ResultInterface $result
     * @return array
     */
    protected function hydrateAggregateRootsFromResult(ResultInterface $result)
    {
        $aggregateRootClassName = $this->getAggregateRootClass();
        $cmdReplyClassName = $this->reply_repository->getAggregateRootClass();
        $aggregateRoots = [];
        foreach ($result as $row) {
            $requestid = $row['requestid'];
            /** @var CommandLogInterface $aggregateRoot */
            if (!array_key_exists($requestid, $aggregateRoots)) {
                $aggregateRoot = new $aggregateRootClassName;
                $this->aggregateRootHydrator->hydrate($row, $aggregateRoot);
                $aggregateRoots[$requestid] = $aggregateRoot;
            } else {
                $aggregateRoot = $aggregateRoots[$requestid];
            }
            if (isset($row['r.requestid'])) {
                $reply = new $cmdReplyClassName;
                $this->reply_repository->getAggregateRootHydrator()->hydrate($row,$reply);
                $aggregateRoot->addReply($reply);
            }
        }
        return array_values($aggregateRoots);
    }


    public function setReplyClass($class){
        $this->reply_class = $class;
        return $this;
    }

    public function getReplyClass(){
        return $this->reply_class;
    }

    public function setReplyHydrator($hydrator){
        $this->reply_hydrator = $hydrator;
        return $this;
    }

    public function getReplyHydrator(){
        return $this->reply_hydrator;
    }

    public function setReplyTableName($table){
        $this->reply_table = $table;
        return $this;
    }

    public function getReplyTableName(){
        return $this->reply_table;
    }

    public function setReplyTableSequenceName($seq){
        $this->reply_table_seq = $seq;
        return $this;
    }

    public function setReplyRepository($class){
        $this->reply_repository = $class;
        return $this;
    }
    public function getReplyRepository(){
        return $this->reply_repository;
    }


}
