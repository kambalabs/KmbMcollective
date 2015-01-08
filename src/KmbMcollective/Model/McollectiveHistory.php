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

class McollectiveHistory implements McollectiveHistoryInterface
{

    /** @var int */
    protected $id;

    /** @var int */
    protected $actionid;

    /** @var int */
    protected $requestid;

    /** @var string */
    protected $caller;

    /** @var string */
    protected $hostname;

    /** @var string */
    protected $agent;

    /** @var string */
    protected $action;

    /** @var string */
    protected $sender;

    /** @var string */
    protected $statuscode;

    /** @var string */
    protected $result;

    /** @var string */
    protected $receivedAt;

    /** @var McollectiveLog[] */
    protected $ihmlogs;

    protected $type;

    protected $actionSummary;

    /**
     * @param string $actionid
     * @param string $requestid
     * @param string $caller
     * @param string $hostname
     * @param string $agent
     * @param string $action
     * @param string $sender
     * @param string $statuscode
     * @param string $result
     * @param string $receivedAt
     */
    public function __construct($actionid = null, $requestid = null, $caller = null, $hostname = null, $agent = null, $action = null, $sender = null, $statuscode = null, $result = null, $receivedAt = null,$type = null)
    {
        $this->setActionid($actionid);
        $this->setRequestId($requestid);
        $this->setCaller($caller);
        $this->setHostname($hostname);
        $this->setAgent($agent);
        $this->setAction($action);
        $this->setSender($sender);
        $this->setStatusCode($statuscode);
        $this->setResult($result);
        $this->setReceivedAt($receivedAt);
        $this->setType($type);
    }

    /**
     * Set Id.
     *
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set Type.
     *
     * @param string $type
     * @return McollectiveHistory
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get Type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set ActionId.
     *
     * @param string $actionid
     * @return McollectiveHistoryInterface
     */
    public function setActionId($actionid)
    {
        $this->actionid = $actionid;
        return $this;
    }

    /**
     * Get ActionId.
     *
     * @return string
     */
    public function getActionId()
    {
        return $this->actionid;
    }

    /**
     * Set RequestId.
     *
     * @param string $requestId
     * @return McollectiveHistoryInterface
     */
    public function setRequestId($requestId)
    {
        $this->requestid = $requestId;
        return $this;
    }

    /**
     * Get RequestId.
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestid;
    }

    /**
     * Set Caller.
     *
     * @param string $caller
     * @return McollectiveHistoryInterface
     */
    public function setCaller($caller)
    {
        $this->caller = $caller;
        return $this;
    }

    /**
     * Get Caller.
     *
     * @return string
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * Set Hostname.
     *
     * @param string $hostname
     * @return McollectiveHistoryInterface
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * Get Hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set Agent.
     *
     * @param string $agent
     * @return McollectiveHistoryInterface
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
        return $this;
    }

    /**
     * Get Agent.
     *
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }


    /**
     * Set Summary.
     *
     */
    public function setSummary($summary)
    {
        $this->actionSummary = $summary;
        return $this;
    }

    /**
     * Get Summary.
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->actionSummary;
    }

     /**
     * Set Action.
     *
     * @param string $action
     * @return McollectiveHistoryInterface
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get Action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

     /**
     * Set Sender.
     *
     * @param string $sender
     * @return McollectiveHistoryInterface
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Get Sender.
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set StatusCode.
     *
     * @param string $statuscode
     * @return McollectiveHistoryInterface
     */
    public function setStatusCode($statuscode)
    {
        $this->statuscode = $statuscode;
        return $this;
    }

    /**
     * Get StatusCode.
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statuscode;
    }

    /**
     * Set Result.
     *
     * @param string $result
     * @return McollectiveHistoryInterface
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get Result.
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }


    /**
     * Set ReceivedAt.
     *
     * @param string $timestamp
     * @return McollectiveHistoryInterface
     */
    public function setReceivedAt($timestamp)
    {
        $this->receivedAt = $timestamp;
        return $this;
    }

    /**
     * Get ReceivedAt.
     *
     * @return string
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    /**
     * Set ihm logs
     *
     * @param string $timestamp
     * @return McollectiveHistoryInterface
     */
    public function setIhmLogs($logs)
    {
        $this->ihmlogs = $logs;
        return $this;
    }

    /**
     * Add ihm logs
     *
     * @param string $timestamp
     * @return McollectiveHistoryInterface
     */
    public function addIhmLogs($log)
    {
        $this->ihmlogs[] = $log;
        return $this;
    }

    /**
     * Get ihm logs.
     *
     * @return string
     */
    public function getIhmLog()
    {
        return $this->ihmlogs;
    }

    /**
     * Retun Json representation.
     *
     * @return string
     */
    public function toArray()
    {
        /**
         * @param string $actionid
         * @param string $requestid
         * @param string $caller
         * @param string $hostname
         * @param string $agent
         * @param string $action
         * @param string $sender
         * @param string $statuscode
         * @param string $result
         * @param string $receivedAt
         **/
        return [
            'actionid' => $this->getActionId(),
            'requestid' => $this->getRequestid(),
            'caller' => $this->getCaller(),
            'hostname' => $this->getHostname(),
            'agent' => $this->getAgent(),
            'action' => $this->getAction(),
            'sender' => $this->getSender(),
            'statuscode' => $this->getStatusCode(),
            'result' => $this->getResult(),
            'received_at' => $this->getReceivedAt()
        ];
    }




}
