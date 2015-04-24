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

use KmbMcollective\Model\McollectiveHistory;
use KmbMcollective\Model\ActionLog;
use KmbMcollective\Model\CommandLog;
use KmbMcollective\Model\CommandReply;
use KmbMcollective\Service\ReplyHandler;
use KmbMcProxy\Service;
use Zend\Log\Logger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ReplyHandler implements ServiceLocatorAwareInterface {

    protected $serviceLocator;
    protected $actionLogRepository;
    protected $commandLogRepository;

    public function process($historyLog,$repository) {
        $log = $repository->getRequestResponse($historyLog->requestid, $historyLog->hostname);
        if(is_bool($log) || empty($log)) {
            $actionid = null;
            if(!isset($historyLog->actionid) && isset($historyLog->statuscode)) {
                if($historyLog->statuscode != 0)
                {
                    $capture = preg_match('/^\[Action id\: ([a-f0-9]+)\].*/', $historyLog->data, $match);
                    error_log(print_r($match,true));
                    if($capture) {
                        $actionid = $match[1];
                    }
                }
            } else {
                $actionid = isset($historyLog->actionid) ? $historyLog->actionid : $historyLog->requestid;
            }
            error_log('Creating new McollectiveHistory');
            $log = new McollectiveHistory($actionid , $historyLog->requestid, isset($historyLog->caller) ? $historyLog->caller : 'unknown' , isset($historyLog->hostname) ? $historyLog->hostname : null , isset($historyLog->agent) ? $historyLog->agent : null, isset($historyLog->senderaction) ? $historyLog->senderaction : null, isset($historyLog->senderid) ? $historyLog->senderid : 'unknown', isset($historyLog->statuscode) ? $historyLog->statuscode : null, isset($historyLog->data) ? $historyLog->data : null, date('Y-m-d G:i:s'), isset($historyLog->type) ? $historyLog->type : null);
            $repository->add($log);
        }else{
            error_log('Updating McollectiveHistory');
            $log = $log[0];
            // Merging existing infos with new ones
            if(isset($historyLog->actionid) && ($log->getActionid() == $log->getRequestId())) {
                $log->setActionId($historyLog->actionid);
            }
            if($log->getCaller() == 'unknown' && isset($historyLog->caller)) { $log->setCaller($historyLog->caller); };
            if($log->getAgent() == null && isset($historyLog->agent)) { $log->setAgent($historyLog->agent); };
            if($log->getAction() == null && isset($historyLog->senderaction)) { $log->setAction($historyLog->senderaction); };
            if($log->getSender() == 'unknown' && isset($historyLog->senderid)) { $log->setSender($historyLog->senderid); };
            if($log->getStatusCode() == null && isset($historyLog->statuscode)) { $log->setStatusCode($historyLog->statuscode); };
            if($log->getResult() == null && isset($historyLog->data)) { $log->setResult($historyLog->data); };
            if($log->getType() == null && isset($historyLog->type)) { $log->setType($historyLog->type); };
            $repository->update($log);
        }
        if ($log->getAgent() != null && $log->getResult() != null) {
            $log->setFinished();
            $repository->update($log);
        }
        if( $log->isFinished() && $log->getType() != 'default' ) {
            error_log('Specific type found...');
            $handlerName = ucfirst($log->getType()).'ReplyHandler';
            $handler = $this->serviceLocator->get($handlerName);
            $handler->process($log);
        }

    }

    public function newprocess($log){
        if(!isset($log->requestid)){
            error_log('[!!!] No requestid found in message!');
            return;
        }
        $command = $this->commandLogRepository->getById($log->requestid);
        $action = null;
        if(! isset($command)){
            $action = new ActionLog($log->requestid);
            $action->setDescription('CLI command from '. $log->hostname);
            $command = new CommandLog($log->requestid);
            $action->addCommand($command);
            $this->actionLogRepository->add($action);
        }else{
            $action = $this->actionLogRepository->getById($command->getActionId());
        }
        $reply = $command->getReplyFor($log->hostname);
        if( ! isset($reply)){
            // No reply inserted for this host
            $reply = new CommandReply();
            $reply->setRequestId($log->requestid);
            $reply->setHostname($log->hostname);
            $command->addReply($reply);
            $this->commandLogRepository->getReplyRepository()->add($reply);
        }

        // Updating infos
        // Warning : ugly code ... must be reviewed...
        if(isset($log->agent) && $reply->getAgent() == null) {
            $reply->setAgent($log->agent);
        }
        if(isset($log->senderagent) && $reply->getAgent() == null) {
            $reply->setAgent($log->senderagent);
        }
        if(isset($log->action) && $reply->getAction() == null) {
            $reply->setAction($log->action);
        }
        if(isset($log->senderaction) && $reply->getAction() == null) {
            $reply->setAction($log->senderaction);
        }
        if(isset($log->caller) && $reply->getUser() == null) {
            $reply->setUser($log->caller);
        }
        if(isset($log->statuscode) && $log->statuscode !== null && $reply->getStatusCode() == null){
            $reply->setStatusCode($log->statuscode);
            $reply->finish(true);
        }
        if(isset($log->data) && $reply->getResult() == null){
            $reply->setResult($log->data);
        }
        $this->commandLogRepository->getReplyRepository()->update($reply);
    }


    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setActionLogRepository($repository){
        $this->actionLogRepository = $repository;
        return $this;
    }

    public function getActionLogRepository(){
        return $this->actionLogRepository;
    }

    public function setCommandLogRepository($repository){
        $this->commandLogRepository = $repository;
        return $this;
    }

    public function getCommandLogRepository(){
        return $this->commandLogRepository;
    }
}
