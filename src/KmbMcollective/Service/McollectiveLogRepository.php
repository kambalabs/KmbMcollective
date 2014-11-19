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
use KmbMcollective\Model\McollectiveLogInterface;
use KmbMcollective\Model\McollectiveLogRepositoryInterface;
use Zend\Db\Sql\Predicate\Predicate;

class McollectiveLogRepository extends Repository implements McollectiveLogRepositoryInterface
{

    /**
     * @param AggregateRootInterface $aggregateRoot
     * @return McollectiveLogInterface
     * @throws \Zend\Db\Exception\ExceptionInterface
     */
    public function add(AggregateRootInterface $aggregateRoot)
    {
        /** @var EnvironmentInterface $aggregateRoot */
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
     * @param $user
     * @return array
     */
    public function getAllByUser($user)
    {
        $criteria = new Predicate();
        return $this->getAllBy($criteria->equalTo('user', $user));
    }

}
