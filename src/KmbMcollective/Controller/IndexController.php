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
namespace KmbMcollective\Controller;

use GtnDataTables\Service\DataTable;
use KmbMcollective\Model\McollectiveAgent;
use KmbMcollective\Model\McollectiveAction;
use KmbMcProxy\Service;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $acceptCriteria = array(
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ),
        'Zend\View\Model\ViewModel' => array(
            'text/html',
        ),
    );

    public function metadataUpdateAction() {
        $params = $this->params()->fromPost();
        $agentName = $params['agent'];
        $agentRepository = $this->getServiceLocator()->get('McollectiveAgentRepository');
        $agent = $agentRepository->getByName($agentName);
        if($agent != null) {
            $this->debug("Updating agent " . $agentName);
            $agent->setDescription($params['agentDesc']);
            foreach($this->params()->fromPost('action') as $actionName => $detail) {
                $action = $agent->getRelatedActions($actionName);
                if($action != null){
                    $action->setDescription($detail['description']);
                    $action->setLongDesc($detail['longdesc']);
                    $action->setShortDesc($detail['shortdesc']);
                    $action->setIhmIcon($detail['ihmicon']);
                    if($detail['limitnumber'] != "" ) {
                        $action->setLimitNumber(intval($detail['limitnumber']));
                    }
                    $action->setLimitHosts($detail['limitHosts']);
                } else {
                    $action = new McollectiveAction($actionName, $detail['description'], $agent->getId(), $detail['longdesc'], $detail['shortdesc'], $detail['ihmicon'], intval($detail['limitnumber']), $detail['limitHosts']);
                    $agent->addRelatedAction($action);
                }
            }
            $agentRepository->update($agent);
        } else {
            $this->debug("Creating new metadata for  agent " . $agentName);
            $agent = new McollectiveAgent();
            $agent->setName($agentName);
            if ($params['agentDesc']) {
                $agent->setDescription($params['agentDesc']);
            }
            $agentRepository->add($agent);
        }
                
        $this->debug("Params : " . print_r($this->params()->fromPost(),true));
        return $this->redirect()->toRoute('mcollective_metadatas', ['agent' => $agentName], [], true);
    }
    
    public function metadataAction() {
        $agent = $this->params()->fromRoute('agent');
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
        $agentList = $mcProxyAgentService->getAll();

        if ($agent == null ) {
            $choiceText = "Choisissez un agent pour ajouter les metadatas.";
            return new ViewModel(['agent' => $agent, 'choiceText' => $choiceText, 'agentList' => $agentList]);
        } else {
            $agentRepository = $this->getServiceLocator()->get('McollectiveAgentRepository');
            $agentDetail = $agentRepository->getByName($agent);
            $this->debug("Agent retrieved : ". print_r($agentDetail,true));
            if ($agentDetail == null) {
                $agentDetail = new McollectiveAgent($agent);
            }
            return new ViewModel(['agent' => $agent, 'agentList' => $agentList, 'agentDetail' => $agentDetail]);
        }
    }
    
    public function showAction()
    {
        $environment = $this->getServiceLocator()->get('EnvironmentRepository')->getById($this->params()->fromRoute('envId'));
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
        $this->debug('KmbMcollective/IndexController::showAction(' . $environment . ')');

        if ($environment == null) {
            $this->globalMessenger()->addDangerMessage($this->translate('<h4>Warning !</h4><p>You have to select an environment first !</p>'));
            return new ViewModel();
        }
        $agents = $mcProxyAgentService->getAll();
        return new ViewModel(['environment' => $environment, 'agents' => $agents]);
    }

    public function agentsAction()
    {
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
        $agents = $mcProxyAgentService->getAll();
        $agentList = [];
        foreach ($agents as $agent) {
            foreach ($agent->getActions() as $action) {
                $agentList[$agent->getName()][$action->getName()] = [
                    'input' => $action->getInputArguments(),
                    'output' => $action->getOutputArguments(),
                    'summary' => $action->getSummary(),
                ];
            }
        }
        return new JsonModel($agentList);
    }

    public function runAction()
    {
        /** @var EnvironmentInterface $environment */
        $environment = $this->getServiceLocator()->get('EnvironmentRepository')->getById($this->params()->fromRoute('envId'));
        /** @var Service\Agent $mcProxyAgentService */
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
        /** @var UserInterface $user */
        $user = $this->identity();
        $actionid = md5($user->getLogin() . time());
        $params = $this->getRequest()->getPost();
        $this->debug('KmbMcollective/IndexController::runAction(' . $environment . ')');
        $args = [];
        foreach (explode(' ', trim($params['args'])) as $argname) {
            $args[$argname] = $params[$argname];
        }
        $actionResult = $mcProxyAgentService->doRequest($params['agent'], $params['action'], $params['filter'], $environment->getNormalizedName(), $user->getLogin(), $args);
        $mcoLog = new McollectiveLog();
        $this->debug('KmbMcollective/IndexController::log(' . $user->getName() . '/' . $actionResult->actionid . ')');
        $mcoLog->setActionid($actionResult->actionid);
        $mcoLog->setLogin($user->getLogin());
        $mcoLog->setFullName($user->getName());
        $mcoLog->setAgent($params['agent'] . '::' . $params['action']);
        $mcoLog->setFilter($params['filter']);
        $mcoLog->setDiscoveredNodes($actionResult->discovered_nodes);
        $mcoLog->setPf($environment->getNormalizedName());
        try {
            $logRepository = $this->getServiceLocator()->get('McollectiveLogRepository');
            $logRepository->add($mcoLog);
        } catch (\Exception $e) {
            $this->debug($e->getMessage());
            $this->debug($e->getTraceAsString());
        }
        $resultUrl = (string)$this->url()->fromRoute('mcollective_history', ['action' => 'history', 'id' => $actionResult->actionid ], [], true);
        $actionResult->resultUrl = $resultUrl;
        return new JsonModel((array)$actionResult);
    }

    public function historyAction()
    {
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        $environment = $this->getServiceLocator()->get('EnvironmentRepository')->getById($this->params()->fromRoute('envId'));
        $id = $this->params()->fromRoute('id');
        if (!empty($id)) {
            $actionid = $this->params()->fromRoute('id');
            $historyClass = $this->getServiceLocator()->get('McollectiveHistoryRepository');
            $state = $this->params()->fromQuery('state');
            if(!empty($state)) {
                $result = $historyClass->getByActionid($actionid,pg_escape_string($state));
            } else {
                $result = $historyClass->getByActionid($actionid);
            }
            $resultList = array();
            $errorcount = 0;
            foreach ($result as $res) {
                if ($res->getStatusCode() != 0) {
                    $errorcount++;
                }
                $resultList[$res->getHostname()][] = $res->toArray();
            }
            if ($viewModel instanceof JsonModel) {
                return new JsonModel($resultList, [], true);
            } elseif ($viewModel instanceof ViewModel) {
                return new ViewModel(['environment' => $environment, 'actionid' => $actionid, 'logs' => $resultList, 'errorcount' => $errorcount]);
            }
        } else {
            $variables = [];
            if ($viewModel instanceof JsonModel) {
                /** @var DataTable $datatable */
                $datatable = $this->getServiceLocator()->get('historyDatatable');
                $params = $this->params()->fromQuery();
                $result = $datatable->getResult($params);
                $variables = [
                    'draw' => $result->getDraw(),
                    'recordsTotal' => $result->getRecordsTotal(),
                    'recordsFiltered' => $result->getRecordsFiltered(),
                    'data' => $result->getData(),
                ];
            }
            $viewModel->setVariables($variables);
            return $viewModel;
        }
    }

    /**
     * @param string $message
     * @return IndexController
     */
    public function debug($message)
    {
        /** @var Logger $logger */
        $logger = $this->getServiceLocator()->get('Logger');
        if ($logger != null) {
            $logger->debug($message);
        }
        return $this;
    }
}
