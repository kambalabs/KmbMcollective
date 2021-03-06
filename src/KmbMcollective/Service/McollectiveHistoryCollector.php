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

use GtnDataTables\Model\Collection;
use GtnDataTables\Service\CollectorInterface;

class McollectiveHistoryCollector implements CollectorInterface
{
    /** @var Service\NodeInterface */
    protected $historyRepository;
    protected $agentRepository;
    protected $userRepository;
    protected $specificMetadata;
    protected $viewHelperManager;

    /**
     * @param array $params
     * @return Collection
     */
    public function findAll(array $params = null)
    {
        $offset = isset($params['start']) ? $params['start'] : null;
        $limit = isset($params['length']) ? $params['length'] : null;
        $query = null;

        if (isset($params['search']['value']) && !empty($params['search']['value'])) {
            $query = $params['search']['value'];
        }
        $orderBy = "";
        if (isset($params['order'])) {
            foreach ($params['order'] as $clause) {
                if(!empty($orderBy)) {
                    $orderBy .= ',';
                }
                $orderBy .= "".$clause['column']." ".$clause['dir'];
            }
        }
        $logsCollection = $this->getHistoryRepositoryService()->getAll($query, $offset, $limit, $orderBy);

        // Add metadata
        foreach($logsCollection as $actionid => $detail) {
            $agentName = $detail->getIhmLog() ? $detail->getIhmLog()[0]->getAgent() : $detail->getAgent();
            $formatter = isset($this->specificMetadata[$agentName]) ? new $this->specificMetadata[$agentName]['formatter']() : new DefaultFormatter($this->agentRepository);
            $formatter->setViewHelperManager($this->viewHelperManager);
            $detail = $formatter->format($detail);

            $user = $this->userRepository->getByLogin($detail->getCaller());
            if(isset($user)){
                $detail->setCaller($user->getName());
            }

        }
        return $logsCollection;
    }

    /**
     * Get HistoryRepositoryService.
     *
     * @return \KmbPuppetDb\Service\HistoryRepository
     */
    public function getHistoryRepositoryService()
    {
        return $this->historyRepository;
    }

    /**
     * Set HistoryRepositoryService.
     *
     * @param \KmbPuppetDb\Service\McollectiveHistoryRepository $historyRepositoryService
     * @return McollectiveLogCollector
     */
    public function setHistoryRepositoryService($historyRepositoryService)
    {
        $this->historyRepository = $historyRepositoryService;
        return $this;
    }

    /**
     * Get AgentRepository.
     *
     */
    public function getAgentRepositoryService()
    {
        return $this->agentRepository;
    }

    /**
     * Set AgentRepository.
     *
     */
    public function setAgentRepository($agentRepository)
    {
        $this->agentRepository = $agentRepository;
        return $this;
    }


    /**
     * Get UserRepository.
     *
     */
    public function getUserRepositoryService()
    {
        return $this->userRepository;
    }

    /**
     * Set UserRepository.
     *
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
        return $this;
    }

    public function getSpecificMetadata()
    {
        return $this->specificMetadata;
    }

    public function setSpecificMetadata($metadatas)
    {
        $this->specificMetadata = $metadatas;
        return $this;
    }

    public function getViewHelperManager()
    {
        return $this->viewHelperManager;
    }

    public function setViewHelperManager($vhm)
    {
        $this->viewHelperManager = $vhm;
        return $this;
    }

}
