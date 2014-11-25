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
use KmbMcollective\Model\McollectiveAgentInterface;
use KmbMcollective\Model\McollectiveAgentRepositoryInterface;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;


class McollectiveAgentRepository extends Repository implements McollectiveAgentRepositoryInterface
{
    /** @var  string */
    protected $actionClass;

    /** @var  HydratorInterface */
    protected $actionHydrator;

    /** @var  string */
    protected $actionTableName;

    /** @var  string */
    protected $actionTableSequenceName;
    

    // protected $actionMetadataTableName = 'mcollective_actions_metadata'; 
    // protected $actionArgumentMetadataTableName = 'mcollective_actions_arguments_metadata';
    /**
     * @param AggregateRootInterface $aggregateRoot
     * @return McollectiveAgentInterface
     * @throws \Zend\Db\Exception\ExceptionInterface
     */
    public function add(AggregateRootInterface $aggregateRoot)
    {
        error_log("Creating new metadata");
        /** @var McollectiveHistoryInterface $aggregateRoot */
        $connection = $this->getDbAdapter()->getDriver()->getConnection()->beginTransaction();
        try {
            /** @var RevisionInterface $aggregateRoot */
            parent::add($aggregateRoot);
            if ($aggregateRoot->hasRelatedActions()) {
                foreach ($aggregateRoot->getRelatedActions() as $action) {
                    $action->setRelatedAgent($aggregateRoot);
                    $this->actionRepository->add($action);
                }
            }

            $connection->commit();
        } catch (ExceptionInterface $e) {
            error_log("Exception while inserting new metadata for an agent.");
            error_log(print_r($e,true));
            $connection->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     * @return RepositoryInterface
     */
    public function update(AggregateRootInterface $aggregateRoot)
    {
        /** @var RevisionInterface $aggregateRoot */
        parent::update($aggregateRoot);

        if ($aggregateRoot->hasRelatedActions()) {
            foreach ($aggregateRoot->getRelatedActions() as $action) {
                $action->setRelatedAgent($aggregateRoot);
                if ($action->getId() === null) {
                    $data = $this->actionHydrator->extract($action);
                    $insert = $this->getMasterSql()->insert($this->actionTableName)->values($data);
                    $this->performWrite($insert);
                    if ($action->getId() === null) {
                        $action->setId($this->getDbAdapter()->getDriver()->getLastGeneratedValue($this->actionTableSequenceName));
                    }
                }
            }
        }        
        return $this;
    }
    

    
    /**
     * @param string $name
     * @return McollectiveAgentInterface
     */
    public function getByName($name)
    {
        $select = $this->getSelect();
        $select->where
            ->equalTo($this->getTableName().'.name',$name);
        return $this->hydrateAggregateRootsFromResult($this->performRead($select))[0];
    }

    public function getAll() {
        return $this->hydrateAggregateRootsFromResult($this->performRead($this->getSelect()));
    }


    /**
     * Set actionClass.
     *
     * @param string $actionClass
     * @return McollectiveAgentRepository
     */
    public function setActionClass($actionClass)
    {
        $this->actionClass = $actionClass;
        return $this;
    }

    /**
     * Get actionClass.
     *
     * @return string
     */
    public function getActionClass()
    {
        return $this->actionClass;
    }

    /**
     * Set actionHydrator.
     *
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $revisionLogHydrator
     * @return McollectiveAgentRepository
     */
    public function setActionHydrator($actionHydrator)
    {
        $this->actionHydrator = $actionHydrator;
        return $this;
    }

    /**
     * Get ActionHydrator.
     *
     * @return \Zend\Stdlib\Hydrator\HydratorInterface
     */
    public function getActionHydrator()
    {
        return $this->actionHydrator;
    }

    /**
     * Set ActionTableName.
     *
     * @param string $actionTableName
     * @return McollectiveAgentRepository
     */
    public function setActionTableName($actionTableName)
    {
        $this->actionTableName = $actionTableName;
        return $this;
    }

    /**
     * Get ActionTableName.
     *
     * @return string
     */
    public function getActionTableName()
    {
        return $this->actionTableName;
    }

    /**
     * Set ActionTableSequenceName.
     *
     * @param string $actionTableSequenceName
     * @return McollectiveAgentRepository
     */
    public function setActionTableSequenceName($actionTableSequenceName)
    {
        $this->actionTableSequenceName = $actionTableSequenceName;
        return $this;
    }

    /**
     * Get ActionTableSequenceName.
     *
     * @return string
     */
    public function getActionTableSequenceName()
    {
        return $this->actionTableSequenceName;
    }

    protected function getSelect()
    {
        $select =  parent::getSelect()
            ->join(
                ['mact' => $this->getActionTableName()],
                $this->getTableName() . '.id = mact.agent_id',
                [
                    'mact.id' => 'id',
                    'mact.agent_id' => 'agent_id',
                    'mact.name' => 'name',
                    'mact.description' => 'description',
                    'mact.long_detail' => 'long_detail',
                    'mact.short_detail' => 'short_detail',
                    'mact.ihm_icon' => 'ihm_icon',
                    'mact.limit_number' => 'limit_number',
                    'mact.limit_host' => 'limit_host',
                ],
                Select::JOIN_LEFT
            );
        error_log($select->getSqlString());
        return $select;
    }

    /**
     * @param ResultInterface $result
     * @return array
     */
    protected function hydrateAggregateRootsFromResult(ResultInterface $result)
    {
        $aggregateRootClassName = $this->getAggregateRootClass();
        $actionClassName = $this->getActionClass();
        $aggregateRoots = [];
        foreach ($result as $row) {
            $agentId = $row['id'];
            if (!array_key_exists($agentId, $aggregateRoots)) {
                $aggregateRoot = new $aggregateRootClassName;
                $aggregateRoots[$agentId] = $this->aggregateRootHydrator->hydrate($row, $aggregateRoot);
            } else {
                $aggregateRoot = $aggregateRoots[$agentId];
            }

            if (isset($row['mact.name'])) {
                /** @var RevisionLog $revisionLog */
                $action = new $actionClassName;
                $this->actionHydrator->hydrate($row, $action);
                $aggregateRoot->addRelatedAction($action);
            }
        }
        return array_values($aggregateRoots);
    }


}
