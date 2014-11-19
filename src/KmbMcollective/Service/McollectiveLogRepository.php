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

use GtnPersistBase\Model\AggregateRootInterface;
use GtnPersistZendDb\Infrastructure\ZendDb\Repository;
use KmbMcollective\Model\McollectiveLogInterface;
use KmbMcollective\Model\McollectiveLogRepositoryInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Exception\ExceptionInterface;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Select;

class McollectiveLogRepository extends Repository implements McollectiveLogRepositoryInterface
{
    /** @var string */
    protected $discoveredNodesTableName = 'mcollective_logs_discovered';

    /**
     * @param AggregateRootInterface $aggregateRoot
     * @return McollectiveLogRepository
     * @throws \Zend\Db\Exception\ExceptionInterface
     */
    public function add(AggregateRootInterface $aggregateRoot)
    {
        $connection = $this->getDbAdapter()->getDriver()->getConnection()->beginTransaction();
        try {
            /** @var McollectiveLogInterface $aggregateRoot */
            parent::add($aggregateRoot);
            foreach ($aggregateRoot->getDiscoveredNodes() as $hostname) {
                $insert = $this->getMasterSql()->insert($this->discoveredNodesTableName)->values([
                    'log_id' => $aggregateRoot->getId(),
                    'hostname' => $hostname,
                ]);
                $this->performWrite($insert);
            }
            $connection->commit();
        } catch (ExceptionInterface $e) {
            $connection->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * @param $actionid
     * @return McollectiveLogInterface
     */
    public function getByActionid($actionid)
    {
        $criteria = new Predicate();
        return $this->getBy($criteria->equalTo('actionid', $actionid));
    }

    /**
     * @param integer $nlogs
     * @return array
     */
    public function getLast($nlogs)
    {
        $select = $this->getSelect()->limit($nlogs)->order('id DESC');
        return $this->hydrateAggregateRootsFromResult($this->performRead($select));
    }

    /**
     * @param $login
     * @return array
     */
    public function getAllByLogin($login)
    {
        $criteria = new Predicate();
        return $this->getAllBy($criteria->equalTo('login', $login));
    }

    protected function getSelect()
    {
        return parent::getSelect()->join(
            ['dn' => $this->discoveredNodesTableName],
            $this->tableName . '.id = dn.log_id',
            ['dn.hostname' => 'hostname'],
            Select::JOIN_LEFT
        );
    }

    protected function hydrateAggregateRootsFromResult(ResultInterface $result)
    {
        $aggregateRootClassName = $this->getAggregateRootClass();
        $aggregateRoots = [];
        foreach ($result as $row) {
            $id = $row['id'];
            /** @var McollectiveLogInterface $aggregateRoot */
            if (!array_key_exists($id, $aggregateRoots)) {
                $aggregateRoot = new $aggregateRootClassName;
                $aggregateRoots[$id] = $this->aggregateRootHydrator->hydrate($row, $aggregateRoot);
            } else {
                $aggregateRoot = $aggregateRoots[$id];
            }

            if (isset($row['hostname'])) {
                $aggregateRoot->addDiscoveredNode($row['hostname']);
            }
        }
        return array_values($aggregateRoots);
    }
}
