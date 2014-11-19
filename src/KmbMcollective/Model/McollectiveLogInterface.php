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

interface McollectiveLogInterface extends  AggregateRootInterface
{
    /**
     * Set User.
     *
     * @param string $login
     * @return UserInterface
     */
    public function setUser($login);

    /**
     * Get User.
     *
     * @return string
     */
    public function getUser();

    /**
     * Set FullName.
     *
     * @param string $name
     * @return UserInterface
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
     * @return UserInterface
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
     * @return UserInterface
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
     * @param string[] discoveredNodes
     * @return UserInterface
     */
    public function setDiscoveredNodes($discoveredNodes);

    /**
     * Get DiscoveredNodes.
     *
     * @return string
     */
    public function getDiscoveredNodes();

    /**
     * Set PF.
     *
     * @param string pf
     * @return UserInterface
     */
    public function setPf($pf);

    /**
     * Get pf
     *
     * @return string
     */
    public function getPf();
}
