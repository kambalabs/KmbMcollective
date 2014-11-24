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

interface McollectiveAgentInterface extends  AggregateRootInterface
{
    /**
     * @param string   $name
     * @param string   $description
     * @param McollectiveAction[]   $relatedActions
     */
    public function __construct($name = null, $description = null, $relatedActions = []);

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
    public function setRelatedActions($relatedactions);

    /**
     * Get Agent's related actions
     *
     * @return McollectiveAction[]
     */
    public function getRelatedActions();

    /**
     * Add an agent related action.
     *
     * @param McollectiveActionInterface $relatedAction
     * @return McollectiveAgentInterface
     */
    public function addRelatedAction($relatedAction);

    /**
     * @return string
     */
    public function __toString();

}
