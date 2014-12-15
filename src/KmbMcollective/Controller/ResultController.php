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
use KmbMcollective\Model\McollectiveArgument;
use KmbMcollective\Model\McollectiveLog;
use KmbMcProxy\Service;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use KmbAuthentication\Controller\AuthenticatedControllerInterface;

class ResultController extends AbstractActionController implements AuthenticatedControllerInterface
{
    protected $acceptCriteria = array(
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ),
        'Zend\View\Model\ViewModel' => array(
            'text/html',
        ),
    );

    public function getResultsAction() {
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        $historyRepository = $this->getServiceLocator()->get('McollectiveHistoryRepository');
        $actionid = $this->params()->fromRoute('actionid');
        $requestid = $this->params()->fromRoute('requestid');
        if(isset($requestid)) {
            $results = $historyRepository->getAllByActionidRequestId($actionid,$requestid,'finished');
        }else{
            $results = $historyRepository->getByActionid($actionid,'finished');
        }
        $this->debug(print_r($results[0],true));
        return new JsonModel(array_map(function($item){
            return $item->toArray();
        },$results));
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
