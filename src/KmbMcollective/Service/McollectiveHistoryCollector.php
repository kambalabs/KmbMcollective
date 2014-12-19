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

use GtnDataTables\Model\Collection;
use GtnDataTables\Service\CollectorInterface;

class McollectiveHistoryCollector implements CollectorInterface
{
    /** @var Service\NodeInterface */
    protected $historyRepository;
    protected $agentRepository;

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
            if($detail->getIhmLog() != null) {
                $agentSplit = explode('::',$detail->getIhmLog()[0]->getAgent());
                $agent = $this->agentRepository->getByName($agentSplit[0]);
                if(isset($agent)) {
                    $foo = array_values(array_filter($agent->getRelatedActions(),function($action) use ($agentSplit) {
                            if($action->getName() == $agentSplit[1]) {
                                return true;
                            }
                            }));
                    $summary = $foo[0]->getShortDesc();
                    $params = json_decode($detail->getIhmLog()[0]->getParameters());

                    if(!empty($params)) {
                        foreach($params as $arg => $value) {
                            $summary = str_replace('#'.$arg.'#', $value,$summary);
                        }
                    }
                    $detail->setSummary($summary);
                }else{
                    error_log($agentSplit[0]." not found..");
                }
            }else{
                error_log("Agent MCO : ".$detail->getAction());
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

}
