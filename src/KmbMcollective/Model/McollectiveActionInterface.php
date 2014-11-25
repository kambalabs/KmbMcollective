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

interface McollectiveActionInterface extends  AggregateRootInterface
{
    /**
     * @param string   $name
     * @param string   $description
     * @param McollectiveAction[]   $relatedActions
     */
    public function __construct($name = null, $description = null, $relatedagent = null, $longdetail = null, $shortdetail = null, $ihmicon = null, $limitnum = null, $limithost = []);

    /**
     * Set Id.
     *
     * @param int $id
     * @return McollectiveLog
     */
    public function setId($id);

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set Agent Name.
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setName($name);

    /**
     * Get Agent Name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set Agent Description.
     *
     * @param string $description
     * @return McollectiveAgent
     */
    public function setDescription($description);

    /**
     * Get Agent Description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Agent's related actions.
     *
     * @param McollectiveActions[]
     * @return McollectiveAgent
     */
    public function setRelatedAgent($relatedagent);

    /**
     * Get Agent's related actions
     *
     * @return McollectiveAction[]
     */
    public function getRelatedAgent();


    /**
     * Set Agent long description.
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setLongDesc($longdesc);

    /**
     * Get Agent long description.
     *
     * @return string
     */
    public function getLongDesc();

    /**
     * Set Agent short description.
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setShortDesc($longdesc);

    /**
     * Get Agent short description.
     *
     * @return string
     */
    public function getShortDesc();

    
    /**
     * Set Agent ihm icon
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setIhmIcon($icon);

    /**
     * Get Agent ihm icon
     *
     * @return string
     */
    public function getIhmIcon();

    /**
     * Set Action number of host impacted
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setLimitNumber($max);

    /**
     * Get Agent max number of host impacted
     *
     * @return string
     */
    public function getLimitNumber();

    /**
     * Set Action's allowed host
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setLimitHosts($hosts);

    /**
     * Get Agent max number of host impacted
     *
     * @return string
     */
    public function getLimitHosts();


    /**
     * @return string
     */
    public function __toString();

}
