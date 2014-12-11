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


class McollectiveHistoryRepository extends Repository implements McollectiveHistoryRepositoryInterface
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
            error_log("Got ". count($result)." results .. expecting : ". $expectedResult);
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


}
