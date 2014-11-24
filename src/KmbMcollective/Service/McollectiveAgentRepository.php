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
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;


class McollectiveAgentRepository extends Repository implements McollectiveAgentRepositoryInterface
{

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
    
    /**
     * @param string $name
     * @return McollectiveAgentInterface
     */
    public function getByName($name)
    {
        $criteria = new Where();
        $criteria->equalTo('name',$name);
        return $this->getBy($criteria);

    }

    public function getAll() {
        return $this->hydrateAggregateRootsFromResult($this->performRead($this->getSelect()));
    }
    

}
