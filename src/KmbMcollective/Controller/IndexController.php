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
namespace KmbMcollective\Controller;

use GtnDataTables\Service\DataTable;
use KmbMcollective\Model\McollectiveAgent;
use KmbMcollective\Model\McollectiveAction;
use KmbMcollective\Model\McollectiveArgument;
use KmbMcollective\Model\McollectiveLog;
use KmbMcollective\Model\ActionLog;
use KmbMcollective\Model\CommandLog;
use KmbMcProxy\Service;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use KmbAuthentication\Controller\AuthenticatedControllerInterface;

class IndexController extends AbstractActionController implements AuthenticatedControllerInterface
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
                    $this->debug("Icon : " . $detail['ihmicon']);
                    if($detail['limitnumber'] != "" ) {
                        $action->setLimitNumber(intval($detail['limitnumber']));
                    }
                    $action->setLimitHosts($detail['limitHosts']);
                } else {
                    $action = new McollectiveAction($actionName, $detail['description'], $agent->getId(), $detail['longdesc'], $detail['shortdesc'], $detail['ihmicon'], intval($detail['limitnumber']), $detail['limitHosts']);
                    $agent->addRelatedAction($action);
                }
                if($detail['arguments'] != null) {
                    foreach($detail['arguments'] as $argname => $settings) {
                        $arg = $action->getArguments($argname);
                        if($arg != null)
                        {
                            $this->debug("Editing argument ". $argname);
                            $arg->setDescription($settings['description']);
                            $arg->setType($settings['type']);
                            if($settings['type'] == 'list') {
                                $arg->setValue($settings['value']);
                            }
                            if(isset($settings['mandatory'])) {
                                $arg->setMandatory(1);
                            } else {
                                $arg->setMandatory(0);
                            }
                        } else {

                            $this->debug("Creating a new argument ". $argname);
                            $arg = new McollectiveArgument($argname, $settings['description'], null, $settings['mandatory'] ? $settings['mandatory'] : null, $settings['type'], $settings['type'] == 'list' ? $settings['value'] : null );
                            $action->addArgument($arg);
                        }

                    }
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
            if ($agentDetail == null) {
                $agentDetail = new McollectiveAgent($agent);
            }
            $this->getServiceLocator()->get('breadcrumb')->findBy('id', 'metadata')->setLabel($agent);
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
        $viewModel = new ViewModel(['environment' => $environment, 'agents' => $agents]);
        return $viewModel;
    }

    public function agentsAction()
    {
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
        $metadataRepository = $this->getServiceLocator()->get('McollectiveAgentRepository');
        $agents = $mcProxyAgentService->getAll();
        $agentList = [];
        foreach ($agents as $agent) {
            $agentMetadata = $metadataRepository->getByName($agent->getName());
            $agentList[$agent->getName()]['description'] = isset($agentMetadata) ? $agentMetadata->getDescription() : null;
            foreach ($agent->getActions() as $action) {
                $actionMetadata = $agentMetadata ? $agentMetadata->getRelatedActions($action->getName()) : null;
                $agentList[$agent->getName()]['actions'][$action->getName()] = [
                    'input' => $action->getInputArguments(),
                    'output' => $action->getOutputArguments(),
                    'summary' => $action->getSummary(),
                ];
                $agentList[$agent->getName()]['actions'][$action->getName()]['description'] = isset($actionMetadata) ? $actionMetadata->getDescription() : null;
                $agentList[$agent->getName()]['actions'][$action->getName()]['limitnum'] = isset($actionMetadata) ? $actionMetadata->getLimitNumber() : null;
                $agentList[$agent->getName()]['actions'][$action->getName()]['limithosts'] = isset($actionMetadata) ? explode(",",$actionMetadata->getLimitHosts()) : null;
                foreach( $agentList[$agent->getName()]['actions'][$action->getName()]['input'] as $argname => $argDetail ) {
                    $this->debug(print_r($agentList[$agent->getName()]['actions'][$action->getName()]['input'],true));
                    $argMetadata = isset($actionMetadata) ? $actionMetadata->getArguments($argname) : null;
                    $agentList[$agent->getName()]['actions'][$action->getName()]['input']->$argname->metadesc = isset($argMetadata) ? $argMetadata->getDescription() : null;
                }
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

        $agentsRepository = $this->getServiceLocator()->get('McollectiveAgentRepository');
        /** @var UserInterface $user */
        $user = $this->identity();
        $actionid = md5($user->getLogin() . time());
        $params = $this->getRequest()->getPost();
        $this->debug('KmbMcollective/IndexController::runAction(' . $environment . ')');
        if(is_a($params['filter'],"Array")) {
            $filter = implode(",",$params['filter']);
        }else{
            $filter = $params['filter'];
        }
        $args = [];
        foreach (explode(' ', trim($params['args'])) as $argname) {
            $args[$argname] = $params[$argname];
        }
        try {
            $actionResult = $mcProxyAgentService->doRequest($params['agent'], $params['action'], $filter, $environment->getNormalizedName(), $user->getLogin(), $args);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->debug($e->getMessage());
            $actionResult = null;
        }
        //        $mcoLog = new McollectiveLog($actionResult ? $actionResult->actionid : $actionid, $user->getLogin(),$user->getName() , $params['agent'] . '::' . $params['action'], $filter, $actionResult ? $actionResult->discovered_nodes : [], $environment->getNormalizedName(),json_encode($args));


        // new way
        $actionLog = new ActionLog($actionResult ? $actionResult->actionid : $actionid);
        $actionLog->setLogin($user->getLogin());
        $actionLog->setFullName($user->getName());
        $actionLog->setParameters(json_encode($args));
        $actionLog->setSource('kamba');
        $actionLog->setEnvironment($environment->getId());
        $commandLog = new CommandLog($actionResult->result[0]);
        $actionLog->addCommand($commandLog);

        $agent = $agentsRepository->getByName($params['agent']);
        $actionName = $params['action'];
        $action = array_values(array_filter($agent->getRelatedActions(),function($action) use ($actionName) {
                    if($action->getName() == $actionName) {
                        return true;
                    }
                }));

        $summary = $action[0]->getShortDesc();
        if(isset($args)){
            foreach($args as $arg => $value){
                $summary = str_replace('#'.$arg.'#', $value,$summary);
            }
        }
        if(isset($summary) && $summary != ""){
            $actionLog->setDescription($summary);
        }else{
            $actionLog->setDescription($params['agent'].'::'.$params['action']);
        }
        $actionLog->setIhmIcon($action[0]->getIhmIcon());
        try {
            //            $this->getServiceLocator()->get('McollectiveLogRepository')->add($mcoLog);
            $this->getServiceLocator()->get('ActionLogRepository')->add($actionLog);
        } catch (\Exception $e) {
            $this->debug($e->getMessage());
            $this->debug($e->getTraceAsString());
        }
        if($actionResult)
        {
            $resultUrl = (string)$this->url()->fromRoute('mcollective_show', ['action' => 'history', 'id' => $actionResult->actionid ], [], true);
            $actionResult->resultUrl = $resultUrl;
            return new JsonModel((array)$actionResult);
        }else{
            $this->getResponse()->setStatusCode(500);
            return new JsonModel([$error]);
        }
    }

    public function translationAction() {
        $translation = [
            'nameFilter' => $this->translate('Server name filter'),
            'receivingDataNr' => $this->translate('Receiving Data: %d of %d nodes'),
            'receivingDataPending' => $this->translate('Receiving data : receiving...'),
            'receivingDataDone' => $this->translate('Receiving data : %d nodes done.'),
            'startingRequest' => $this->translate('Starting request : <span class="label label-primary">Running</span>'),
            'requestStarted' => $this->translate('Request started <span class="label label-success">OK</span>'),
        ];
        return new JsonModel($translation);
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
                $result = $historyClass->getByActionid($actionid,$state);
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
                $this->getServiceLocator()->get('breadcrumb')->findBy('id', 'history')->setLabel($actionid);
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

    public function showDetailAction(){
        // new history only for SSDatatable
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        $environment = $this->getServiceLocator()->get('EnvironmentRepository')->getById($this->params()->fromRoute('envId'));
        $actionid = $this->params()->fromRoute('id');
        $actionLogRepository = $this->getServiceLocator()->get('ActionLogRepository');

        $action = $actionLogRepository->getById($actionid);
        $status = $action->getGlobalStatus();


        if ($viewModel instanceof JsonModel) {
            return new JsonModel($action->getResultByHost(), [], true);
        } elseif ($viewModel instanceof ViewModel) {
            $this->getServiceLocator()->get('breadcrumb')->findBy('id', 'history')->setLabel($actionid);
            return new ViewModel(['environment' => $environment, 'actionid' => $actionid, 'logs' => $action->getResultByHost(), 'errorcount' => $status['errors']]);
        }
    }


    public function historyTableAction(){
        // new history only for SSDatatable
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        $environment = $this->getServiceLocator()->get('EnvironmentRepository')->getById($this->params()->fromRoute('envId'));
        $actionid = $this->params()->fromRoute('id');

        $variables = [];
        if ($viewModel instanceof JsonModel) {
            /** @var DataTable $datatable */
            $datatable = $this->getServiceLocator()->get('ActionDatatable');
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
