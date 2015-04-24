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

use KmbMcollective\Model\McollectiveHistory;
use KmbMcollective\Service\ReplyHandler;
use KmbMcProxy\Service;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class ReplyController extends AbstractActionController
{

    protected $acceptCriteria = array(
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        )
    );

    public function processAction() {
        $logtype = $this->params()->fromPost('type');
        $logtype = isset($logtype) ? $logtype : 'default';
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        $historyRepository = $this->getServiceLocator()->get('McollectiveHistoryRepository');
        $handler = $this->getServiceLocator()->get('KmbMcollective\Service\ReplyHandler');
        $response = json_decode($this->getRequest()->getContent());
        $handler->process($response,$historyRepository);

        $viewModel->setVariable("status", 'ok');
        return new $viewModel;
    }

    public function newprocessAction(){
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        $handler = $this->getServiceLocator()->get('ReplyHandler');
        $response = json_decode($this->getRequest()->getContent());
        $handler->newprocess($response);

        $viewModel->setVariable("status", 'ok');
        return new $viewModel;
    }

    public function getHandlerList() {
        return $this->handlerList;
    }

    public function setHandlerList($handlerList) {
        $this->handlerList = $handlerList;
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
