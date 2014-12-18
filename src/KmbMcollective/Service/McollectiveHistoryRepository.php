<?php
/**
 * @copyright Copyright (c) 2014 Orange Applications for Business
 * @link      http://github.com/kambalabs for the sources repositories
 *
 * This file is part of Kamba.
 *
 * Kamba is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Kamba is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kamba.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace KmbMcollective\Service;

use GtnPersistZendDb\Infrastructure\ZendDb\Repository;
use GtnPersistBase\Model\AggregateRootInterface;
use GtnPersistBase\Model\RepositoryInterface;
use KmbMcollective\Model\McollectiveHistoryInterface;
use KmbMcollective\Model\McollectiveHistoryRepositoryInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\Driver\ResultInterface;


class McollectiveHistoryRepository extends Repository implements McollectiveHistoryRepositoryInterface
{


    protected $logTableName;
    protected $logTableSequenceName;
    protected $logHydrator;
    protected $logClass;

    /**
     * @param AggregateRootInterface $aggregateRoot
     * @return McollectiveHistoryInterface
     * @throws \Zend\Db\Exception\ExceptionInterface
     */
    public function add(AggregateRootInterface $aggregateRoot)
    {
        /** @var McollectiveHistoryInterface $aggregateRoot */
        $connection = $this->getDbAdapter()->getDriver()->getConnection()->beginTransaction();
        try {
            /** @var RevisionInterface $aggregateRoot */
            parent::add($aggregateRoot);
            $connection->commit();
        } catch (ExceptionInterface $e) {
            $connection->rollback();
            throw $e;
        }

        return $this;
    }



    public function getFilteredLogs($query = null, $offset = null, $limit = null, $orderBy = null)
    {
        if (is_int($query)) {
            $orderBy = $limit;
            $limit = $offset;
            $offset = $query;
            $query = null;
        }

        //        $selectLogs = $this->getSlaveSql()->select()->from($this->tableName);
        $selectLogs = $this->getJoinSelect();
        if($query != null) {
                $selectLogs->where
                ->like('agent','%'.$query.'%')
                ->or
                ->like('filter','%'.$query.'%')
                ->or
                ->like('fullname', '%'.$query.'%')
                ->or
                ->like('login','%'.$query.'%');
        }
        if($offset != null) {
                $selectLogs->offset($offset);
        }
        if($limit != null) {
            $selectLogs->limit($limit);
        }
        if($orderBy != null) {
            $selectLogs->order($orderBy);
        } else {
            $selectLogs->order('mcollective_actions_logs.received_at DESC');
        }



        // $select = $this
        //     ->getSlaveSql()
        //     ->select()
        //     ->from($this->tableName);

        // if($orderBy != null) {
        //     $select->order($orderBy);
        // } else {
        //     $select->order('received_at DESC');
        // }

        error_log($selectLogs->getSqlString());
        $res = $this->hydrateAggregateRootsFromResult($this->performRead($selectLogs));
        return $res;
    }

    public function getNumberOfRows($query)
    {
        $selectLogs = $this->getSlaveSql()->select()->from($this->tableName)->columns(array('number' => new \Zend\Db\Sql\Expression('COUNT(*)')));;
        if($query != null) {
                $selectLogs->where
                ->like('agent','%'.$query.'%')
                ->or
                ->like('filter','%'.$query.'%')
                ->or
                ->like('fullname', '%'.$query.'%')
                ->or
                ->like('login','%'.$query.'%');
        }
        return $this->performRead($selectLogs);
    }



    /**
     * @param $actionid
     * @return McollectiveHistoryInterface
     */
    public function getByActionid($actionid,$state = null)
    {
        $where = new Where();
        $where -> equalTo('actionid',$actionid);
        if(!empty($state)) {
            $where
                ->and
                ->isNotNull('statuscode');
        }
        $select = $this->getSelect()->where($where);
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));
    }

    public function getResultsByActionid($actionid,$expectedResults,$maxiteration=null) {
        $where = new Where();
        $where -> equalTo('actionid',$actionid);
        $where
            ->and
            ->isNotNull('statuscode');
        $select = $this->getSelect()->where($where);
        error_log($select->getSqlString());
        return $this->getResultSetFor($select,$expectedResults,$maxiteration);
    }

    public function getAllByActionidRequestId($actionid,$requestid,$state = null)
    {
        $where = new Where();
        $where -> equalTo('actionid',$actionid);
        $where -> equalTo('requestid',$requestid);
        if(!empty($state)) {
            $where
                ->and
                ->isNotNull('statuscode');
        }
        $select = $this->getSelect()->where($where);
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));
    }

    public function getResultsByActionidRequestId($actionid, $requestid, $expectedResults,$maxiteration=null) {
        $where = new Where();
        $where -> equalTo('actionid',$actionid);
        $where -> equalTo('requestid',$requestid);
        $where
            ->and
            ->isNotNull('statuscode');
        $select = $this->getSelect()->where($where);
        return $this->getResultSetFor($select,$expectedResults,$maxiteration);
    }

    public function getAll() {
        return $this->hydrateAggregateRootsFromResult($this->performRead($this->getSelect()));
    }


    public function getResultSetFor($request, $expectedResult, $maxiteration=null) {
        $result = $this->hydrateAggregateRootsFromResult($this->performRead($request));
        for($i=0; count($result) < $expectedResult ; $i++) {
            if(isset($maxiteration)) {
                if($i > $maxiteration) {
                    break;
                }
            }
            $result = $this->hydrateAggregateRootsFromResult($this->performRead($request));
            sleep(1);
        }
        return $result;
    }

    /**
     * @param integer $nlogs
     * @return array
     */
    public function getLast($nlogs)
    {
        $select = $this->getSelect()->order('received_at DESC')->limit($nlogs);
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));
    }

    protected function getJoinSelect()
    {
        return parent::getSelect()->join(
            ['log' => $this->logTableName],
            $this->tableName . '.actionid = log.actionid',
            [
                'log.id' => 'id',
                'log.actionid' => 'actionid',
                'log.login' => 'login',
                'log.fullname' => 'fullname',
                'log.agent' => 'agent',
                'log.filter' => 'filter',
                'log.pf' => 'pf',
                'log.parameters' => 'parameters',

            ],
            Select::JOIN_LEFT
        );
        /*->join(
            ['dn' => $this->discoveredNodesTableName],
            $this->tableName . '.id = dn.log_id',
            ['hostname' => 'hostname'],
            Select::JOIN_LEFT
            );*/
    }


    protected function hydrateAggregateRootsFromResult(ResultInterface $result)
    {
        $aggregateRootClassName = $this->getAggregateRootClass();
        $logClassName = $this->getLogClass();
        $aggregateRoots = [];
        foreach ($result as $row) {
            $historyId = $row['id'];
            if (!array_key_exists($historyId, $aggregateRoots)) {
                $aggregateRoot = new $aggregateRootClassName;
                $aggregateRoots[$historyId] = $this->aggregateRootHydrator->hydrate($row, $aggregateRoot);
            } else {
                $aggregateRoot = $aggregateRoots[$historyId];
            }

            if (isset($row['log.actionid'])) {
                $log = new $logClassName;
                $this->logHydrator->hydrateFromJoin($row, $log);
                $aggregateRoot->addIhmLogs($log);
            }
        }
        return array_values($aggregateRoots);
    }


    public function setLogTableName($logtable) {
        $this->logTableName = $logtable;
        return $this;
    }

    public function getLogTableName() {
        return $this->logTableName;
    }

    public function setLogTableSequenceName($logtableseq) {
        $this->logTableSequenceName = $logtableseq;
        return $this;
    }

    public function getLogTableSequenceName() {
        return $this->logTableSequenceName;
    }

    public function setLogClass($logclass) {
        $this->logClassName = $logclass;
        return $this;
    }

    public function getLogClass() {
        return $this->logClassName;
    }

    public function setLogHydrator($loghydrator) {
        $this->logHydrator = $loghydrator;
        return $this;
    }

    public function getLogHydrator() {
        return $this->logHydrator;
    }

}
