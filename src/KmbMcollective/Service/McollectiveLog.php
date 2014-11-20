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


use Zend\Http;
use Zend\Log\Logger;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use KmbMcollective\Service;
use KmbMcollective\Model;
use KmbMcollective;
use GtnDataTables\Model\Collection;
use GtnDataTables\Service\CollectorInterface;



class McollectiveLog implements McollectiveLogInterface
{
    /**
     * @var McollectiveLogRepositoryInterface
     */
    protected $mcollectiveLogRepository;

    /** @var Logger */
    protected $logger;

    /**
     * Retrieves all logs matching with specified query, paging and sorting.
     * All logs are returned if parameters are null.
     * $query can be omitted :
     *    $nodeService->getAll(10, 10);
     *
     * @param mixed $query
     * @param int   $offset
     * @param mixed $limit
     * @param array $orderBy
     * @return array
     */
    public function getAll($query = null, $offset = null, $limit = null, $orderBy = null)
    {
        $logs = $this->mcollectiveLogRepository->getFilteredLogs($query, $offset, $limit, $orderBy);
        $this->logger->debug($logs);
        return Collection::factory($logs,sizeof($logs),sizeof($logs));
    }

    /**
     * Set Logger.
     *
     * @param Logger $logger
     * @return Node
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Get Logger.
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function setMcollectiveLogRepository($mcollectiveLogRepository) {
        $this->mcollectiveLogRepository = $mcollectiveLogRepository;
        return $this;
    }

    public function getMcollectiveLogRepository() {
        return $this->mcollectiveLogRepository;
    }
    
}
