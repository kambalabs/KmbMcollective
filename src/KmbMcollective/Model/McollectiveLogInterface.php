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

interface McollectiveLogInterface extends AggregateRootInterface
{
    /**
     * Set Action Id.
     *
     * @param string $actionid
     * @return McollectiveLogInterface
     */
    public function setActionid($actionid);

    /**
     * Get Action Id.
     *
     * @return string
     */
    public function getActionid();

    /**
     * Set User login.
     *
     * @param string $login
     * @return McollectiveLogInterface
     */
    public function setLogin($login);

    /**
     * Get User login.
     *
     * @return string
     */
    public function getLogin();

    /**
     * Set FullName.
     *
     * @param string $name
     * @return McollectiveLogInterface
     */
    public function setFullName($name);

    /**
     * Get FullName.
     *
     * @return string
     */
    public function getFullName();

    /**
     * Set Agent.
     *
     * @param string $mcoagent
     * @return McollectiveLogInterface
     */
    public function setAgent($mcoagent);

    /**
     * Get Agent.
     *
     * @return string
     */
    public function getAgent();

    /**
     * Set Filter.
     *
     * @param string $filter
     * @return McollectiveLogInterface
     */
    public function setFilter($filter);

    /**
     * Get Filter.
     *
     * @return string
     */
    public function getFilter();

    /**
     * Set DiscoveredNodes.
     *
     * @param string[] $discoveredNodes
     * @return McollectiveLogInterface
     */
    public function setDiscoveredNodes($discoveredNodes);

    /**
     * Add a DiscoveredNode.
     *
     * @param string $discoveredNode
     * @return McollectiveLogInterface
     */
    public function addDiscoveredNode($discoveredNode);

    /**
     * Get DiscoveredNodes.
     *
     * @return string[]
     */
    public function getDiscoveredNodes();

    /**
     * @return bool
     */
    public function hasDiscoveredNodes();

    /**
     * Set PF.
     *
     * @param string $pf
     * @return McollectiveLogInterface
     */
    public function setPf($pf);

    /**
     * Get pf
     *
     * @return string
     */
    public function getPf();
}
