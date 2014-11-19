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
namespace KmbMcollective\Model;

use GtnPersistBase\Model\AggregateRootInterface;

interface McollectiveHistoryInterface extends  AggregateRootInterface
{
    /**
     * Set ActionId.
     *
     * @param string $actionid
     * @return McollectiveHistoryInterface
     */
    public function setActionId($actionid);

    /**
     * Get ActionId.
     *
     * @return string
     */
    public function getActionId();

    /**
     * Set RequestId.
     *
     * @param string $requestId
     * @return McollectiveHistoryInterface
     */
    public function setRequestId($requestId);

    /**
     * Get RequestId.
     *
     * @return string
     */
    public function getRequestId();

    /**
     * Set Caller.
     *
     * @param string $caller
     * @return McollectiveHistoryInterface
     */
    public function setCaller($caller);

    /**
     * Get Caller.
     *
     * @return string
     */
    public function getCaller();

    /**
     * Set Hostname.
     *
     * @param string $hostname
     * @return McollectiveHistoryInterface
     */
    public function setHostname($hostname);

    /**
     * Get Hostname.
     *
     * @return string
     */
    public function getHostname();
    
    /**
     * Set Agent.
     *
     * @param string $agent
     * @return McollectiveHistoryInterface
     */
    public function setAgent($agent);

    /**
     * Get Agent.
     *
     * @return string
     */
    public function getAgent();

     /**
     * Set Action.
     *
     * @param string $action
     * @return McollectiveHistoryInterface
     */
    public function setAction($action);

    /**
     * Get Action.
     *
     * @return string
     */
    public function getAction();

     /**
     * Set Sender.
     *
     * @param string $sender
     * @return McollectiveHistoryInterface
     */
    public function setSender($sender);

    /**
     * Get Sender.
     *
     * @return string
     */
    public function getSender();

    /**
     * Set StatusCode.
     *
     * @param string $statuscode
     * @return McollectiveHistoryInterface
     */
    public function setStatusCode($statuscode);

    /**
     * Get StatusCode.
     *
     * @return string
     */
    public function getStatusCode();

    /**
     * Set Result.
     *
     * @param string $result
     * @return McollectiveHistoryInterface
     */
    public function setResult($result);

    /**
     * Get Result.
     *
     * @return string
     */
    public function getResult();

    /**
     * Set ReceivedAt.
     *
     * @param string $timestamp
     * @return McollectiveHistoryInterface
     */
    public function setReceivedAt($timestamp);

    /**
     * Get ReceivedAt.
     *
     * @return string
     */
    public function getReceivedAt();
    
}
