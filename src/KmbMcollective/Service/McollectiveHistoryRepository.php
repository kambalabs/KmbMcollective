<?php
/**
 * @copyright Copyright (c) 2014, 2015 Orange Applications for Business
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
use KmbMcollective\Model\McollectiveHistoryInterface;
use KmbMcollective\Model\McollectiveHistoryRepositoryInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Stdlib\Hydrator\HydratorInterface;

class McollectiveHistoryRepository extends Repository implements McollectiveHistoryRepositoryInterface
{
    /** @var  string */
    protected $logTableName;

    /** @var  string */
    protected $logTableSequenceName;

    /** @var  HydratorInterface */
    protected $logHydrator;

    /** @var  string */
    protected $logClass;

    /** @var string */
    protected $discoveredNodesTableName = 'mcollective_logs_discovered';

    public function getFilteredLogs($query = null, $offset = null, $limit = null, $orderBy = null, $hostlist = [])
    {
        $selectActionid = $this->getSlaveSql()->select()->quantifier('DISTINCT')->columns(['actionid' => 'actionid', 'received_at' => new Expression('min(received_at)')])->from($this->tableName)->group('actionid');
        $selectActionid = $this->applyConstraintOnQuery($selectActionid, $offset, $limit, $orderBy, $hostlist);

        $actionids = [];
        foreach ($this->performRead($selectActionid) as $result) {
            $actionids[] = $result['actionid'];
        }

        if(empty($actionids)) {
            return null;
        }

        $selectLogs = $this->getJoinSelect();
        $selectLogs = $this->applyConstraintOnQuery($selectLogs, null, null, $orderBy, $hostlist);
        if ($query != null) {
            $selectLogs->where
                ->like('agent', '%' . $query . '%')
                ->or
                ->like('filter', '%' . $query . '%')
                ->or
                ->like('fullname', '%' . $query . '%')
                ->or
                ->like('login', '%' . $query . '%');
        }

        $selectLogs->where([
            'mcollective_actions_logs.actionid' => $actionids,
        ]);
        $res = $this->hydrateAggregateRootsFromResultGroupByActionid($this->performRead($selectLogs));
        return $res;
    }

    public function getNumberOfRows($query)
    {
        $selectLogs = $this->getSlaveSql()->select()->from($this->tableName);
        if ($query != null) {
            $selectLogs->where
                ->like('agent', '%' . $query . '%')
                ->or
                ->like('filter', '%' . $query . '%')
                ->or
                ->like('fullname', '%' . $query . '%')
                ->or
                ->like('login', '%' . $query . '%');
        }
        $selectLogs->columns(array('number' => new \Zend\Db\Sql\Expression('COUNT(DISTINCT(actionid))')));
        return $this->performRead($selectLogs);
    }

    /**
     * @param $actionid
     * @return McollectiveHistoryInterface
     */
    public function getByActionid($actionid, $state = null)
    {
        $where = new Where();
        $where->equalTo('actionid', $actionid);
        if (!empty($state)) {
            $where
                ->and
                ->isNotNull('statuscode')
                ->and
                ->equalTo('finished', 't');
        }
        $select = $this->getSelect()->where($where);
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));
    }

    public function getResultsByActionid($actionid, $expectedResults, $maxiteration = null)
    {
        $where = new Where();
        $where->equalTo('actionid', $actionid);
        $where
            ->and
            ->isNotNull('statuscode');
        $select = $this->getSelect()->where($where);
        return $this->getResultSetFor($select, $expectedResults, $maxiteration);
    }

    public function getAllByActionidRequestId($actionid, $requestid, $state = null)
    {
        $where = new Where();
        $where->equalTo('actionid', $actionid);
        $where->equalTo('requestid', $requestid);
        if (!empty($state)) {
            $where
                ->and
                ->isNotNull('statuscode')
                ->and
                ->equalTo('finished', 't');
        }
        $select = $this->getSelect()->where($where);
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));
    }

    public function getResultsByActionidRequestId($actionid, $requestid, $expectedResults, $maxiteration = null)
    {
        $where = new Where();
        $where->equalTo('actionid', $actionid);
        $where->equalTo('requestid', $requestid);
        $where
            ->and
            ->isNotNull('statuscode');
        $select = $this->getSelect()->where($where);
        return $this->getResultSetFor($select, $expectedResults, $maxiteration);
    }

    public function getAll()
    {
        return $this->hydrateAggregateRootsFromResult($this->performRead($this->getSelect()));
    }

    public function getResultSetFor($request, $expectedResult, $maxiteration = null)
    {
        $result = $this->hydrateAggregateRootsFromResult($this->performRead($request));
        for ($i = 0; count($result) < $expectedResult; $i++) {
            if (isset($maxiteration)) {
                if ($i > $maxiteration) {
                    break;
                }
            }
            $result = $this->hydrateAggregateRootsFromResult($this->performRead($request));
            sleep(1);
        }
        return $result;
    }

    public function getRequestResponse($requestid, $hostname)
    {
        $select = $this->getSelect();
        $select->where
            ->equalTo('requestid', $requestid)
            ->and
            ->equalTo('hostname', $hostname);
        $select->limit(1);
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));
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

    protected function applyConstraintOnQuery($request, $offset = null, $limit = null, $orderBy = null, $hostlist = [])
    {
        if ($offset != null) {
            $request->offset($offset);
        }
        if ($limit != null) {
            $request->limit($limit);
        }
        if ($orderBy != null) {
            $request->order($orderBy);
        } else {
            $request->order('received_at DESC');
        }
        return $request;
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
        )->join(
                ['dn' => $this->discoveredNodesTableName],
                'log.id = dn.log_id',
                ['dn.hostname' => 'hostname'],
                Select::JOIN_LEFT
            );
    }

    protected function hydrateAggregateRootsFromResultGroupByActionid(ResultInterface $result)
    {
        $aggregateRootClassName = $this->getAggregateRootClass();
        $logClassName = $this->getLogClass();
        $aggregateRoots = [];
        foreach ($result as $row) {
            $historyId = $row['actionid'];
            if (!array_key_exists($historyId, $aggregateRoots)) {
                $aggregateRoot = new $aggregateRootClassName;
                $aggregateRoots[$historyId] = $this->aggregateRootHydrator->hydrate($row, $aggregateRoot);
            } else {
                $aggregateRoot = $aggregateRoots[$historyId];
            }

            if (isset($row['log.actionid'])) {
                $agLog = $aggregateRoot->getIhmLog();
                if (empty($agLog)) {
                    $log = new $logClassName;
                    $this->logHydrator->hydrateFromJoin($row, $log);
                    $aggregateRoot->addIhmLogs($log);
                }
                if (isset($row['dn.hostname'])) {
                    if (!in_array($row['dn.hostname'], $log->getDiscoveredNodes())) {
                        $log->addDiscoveredNode($row['dn.hostname']);
                    }
                }
            }
        }
        return array_values($aggregateRoots);
    }

    public function setLogTableName($logtable)
    {
        $this->logTableName = $logtable;
        return $this;
    }

    public function getLogTableName()
    {
        return $this->logTableName;
    }

    public function setLogTableSequenceName($logtableseq)
    {
        $this->logTableSequenceName = $logtableseq;
        return $this;
    }

    public function getLogTableSequenceName()
    {
        return $this->logTableSequenceName;
    }

    public function setLogClass($logclass)
    {
        $this->logClassName = $logclass;
        return $this;
    }

    public function getLogClass()
    {
        return $this->logClassName;
    }

    public function setLogHydrator($loghydrator)
    {
        $this->logHydrator = $loghydrator;
        return $this;
    }

    public function getLogHydrator()
    {
        return $this->logHydrator;
    }
}
