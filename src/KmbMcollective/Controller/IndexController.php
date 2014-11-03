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

use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use KmbMcProxy\Service;

class IndexController extends AbstractActionController
{
    public function showAction()
    {
        $environment = $this->getServiceLocator()->get('EnvironmentRepository')->getById($this->params()->fromRoute('envId'));
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
        $this->debug('KmbMcollective/IndexController::showAction(' . $environment .')');

        if ($environment == null) {
            return new ViewModel(['error' => $this->translate('You have to select an environment first !'), 'environment' => $environment]);
        }
        $agents = $mcProxyAgentService->getAll();
        return new ViewModel(['environment' => $environment, 'agents' => $agents]);
    }

    public function agentsAction()
    {
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
        $agents = $mcProxyAgentService->getAll();
        $agentList = [];
        foreach($agents as $agent)
        {
            foreach($agent->getActions() as $action)
            {
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
        $environment = $this->getServiceLocator()->get('EnvironmentRepository')->getById($this->params()->fromRoute('envId'));
        $mcProxyAgentService = $this->getServiceLocator()->get('mcProxyAgentService');
  
        $actionid = md5($_SERVER['cuid'] . time());
        $params = $this->getRequest()->getPost();
        $this->debug('KmbMcollective/IndexController::runAction(' . $environment .')');
        $args = [];
        foreach(split(' ',trim($params['args'])) as $argname)
        {
            $args[$argname] = $params[$argname];
        }
        $actionResult = $mcProxyAgentService->doRequest($params['agent'],$params['action'],$params['filter'],$environment->getNormalizedName(), 'nxlm5803',$args);
        return new JsonModel((array)$actionResult);
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
