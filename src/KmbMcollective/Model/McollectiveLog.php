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

class McollectiveLog implements McollectiveLogInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $actionid;

    /** @var string */
    protected $login;

    /** @var string */
    protected $fullName;

    /** @var string */
    protected $agent;

    /** @var string */
    protected $filter;

    /** @var string */
    protected $discoveredNodes = [];

    /** @var string */
    protected $pf;

    /** @var int */
    protected $receivedAt;

    /** @var string */
    protected $parameters;

    /**
     * @param string   $actionid
     * @param string   $login
     * @param string   $fullname
     * @param string   $agent
     * @param string   $filter
     * @param string[] $discoveredNodes
     * @param string   $pf
     */
     public function __construct($actionid = null, $login = null, $fullname = null, $agent = null, $filter = null, $discoveredNodes = [], $pf = null)
    {
        $this->setActionid($actionid);
        $this->setLogin($login);
        $this->setFullName($fullname);
        $this->setAgent($agent);
        $this->setFilter($filter);
        $this->setDiscoveredNodes($discoveredNodes);
        $this->setPf($pf);
        $this->setReceivedAt();
    }

    /**
     * Set Id.
     *
     * @param int $id
     * @return McollectiveLog
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
     * Set Action Id.
     *
     * @param string $actionid
     * @return McollectiveLog
     */
    public function setActionid($actionid)
    {
        $this->actionid = $actionid;
        return $this;
    }

    /**
     * Get Action Id.
     *
     * @return string
     */
    public function getActionid()
    {
        return $this->actionid;
    }

    /**
     * Set login.
     *
     * @param string $login
     * @return McollectiveLog
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Get login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set FullName.
     *
     * @param string $fullname
     * @return McollectiveLog
     */
    public function setFullName($fullname)
    {
        $this->fullName = $fullname;
        return $this;
    }

    /**
     * Get FullName.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set Agent.
     *
     * @param string $agent
     * @return McollectiveLog
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
     * Set Filter.
     *
     * @param string $filter
     * @return McollectiveLog
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Get Filter.
     *
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set DiscoveredNodes.
     *
     * @param string $discoveredNodes
     * @return McollectiveLog
     */
    public function setDiscoveredNodes($discoveredNodes)
    {
        $this->discoveredNodes = $discoveredNodes;
        return $this;
    }

    /**
     * Add a DiscoveredNode.
     *
     * @param string $discoveredNode
     * @return McollectiveLogInterface
     */
    public function addDiscoveredNode($discoveredNode)
    {
        $this->discoveredNodes[] = $discoveredNode;
        return $this;
    }

    /**
     * Get DiscoveredNodes.
     *
     * @return string
     */
    public function getDiscoveredNodes()
    {
        return $this->discoveredNodes;
    }

    /**
     * @return bool
     */
    public function hasDiscoveredNodes()
    {
        return !empty($this->discoveredNodes);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLogin();
    }

    /**
     * Get Pf
     *
     * @return string
     */
    public function getPf()
    {
        return $this->pf;
    }

    /**
     * Set Pf
     *
     * @param  string $pf
     * @return McollectiveLog
     */
    public function setPf($pf)
    {
        $this->pf = $pf;
        return $this;
    }

    /**
     * Get Parameters
     *
     * @return int
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set parameters
     *
     * @param  string $parameters
     * @return McollectiveLog
     */
    public function setParameters($parameters = null)
    {
        $this->parameters = $parameters;
        return $this;
    }



    
    /**
     * Get Received date
     *
     * @return int
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    /**
     * Set Log receive date
     *
     * @param  string $timestamp
     * @return McollectiveLog
     */
    public function setReceivedAt($timestamp = null)
    {
        if($timestamp != null) {
            $this->receivedAt = $timestamp;
        } else {
            $this->receivedAt = date('Y-m-d G:i:s');
        }
        return $this;
    }
}
