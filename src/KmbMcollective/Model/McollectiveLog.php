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
    protected $actionid;

    /** @var string */
    protected $user;

    /** @var string */
    protected $fullName;

    /** @var string */
    protected $agent;

    /** @var string */
    protected $filter;

    /** @var string */
    protected $discoveredNodes;

    /** @var string */
    protected $pf;

    

    /**
     * @param string $actionid
     * @param string $user
     * @param string $fullname
     * @param string $agent
     * @param string $filter
     * @param string[] $discoveredNodes
     */
    public function __construct($actionid = null, $user = null, $fullname = null, $agent = null, $filter = null, $discoveredNodes = null, $pf = null)
    {
        $this->setActionid($actionid);
        $this->setUser($user);
        $this->setFullName($fullname);
        $this->setAgent($agent);
        $this->setFilter($filter);
        $this->setDiscoveredNodes($discoveredNodes);
        $this->setPf($pf);
    }

    /**
     * Set Id.
     *
     * @param string $id
     * @return User
     */
    public function setActionid($actionid)
    {
        $this->actionid = $actionid;
        return $this;
    }

    /**
     * Get Id.
     *
     * @return string
     */
    public function getActionid()
    {
        return $this->actionid;
    }



    /**
     * Set Id.
     *
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->actionid = $id;
        return $this;
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->actionid;
    }

    
    /**
     * Set user.
     *
     * @param string $user
     * @return McollectiveLog
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set FullName.
     *
     * @param string $name
     * @return McollectiveLog
     */
    public function setFullName($fullname)
    {
        $this->fullname = $fullname;
        return $this;
    }

    /**
     * Get FullName.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullname;
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
     * Get DiscoveredNodes.
     *
     * @return string
     */
    public function getDiscoveredNodes()
    {
        return $this->discoveredNodes;
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
     * @return string
     */
    public function setPf($pf)
    {
        $this->pf = $pf;
        return $this;
    }
}
