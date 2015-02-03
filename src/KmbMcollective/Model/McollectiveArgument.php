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

class McollectiveArgument implements McollectiveArgumentInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var McollectiveAgent */
    protected $relatedAction;

    protected $mandatory;

    protected $type;

    protected $value;

    /**
     * @param string   $name
     * @param string   $description
     * @param McollectiveAction[]   $relatedActions
     */
    public function __construct($name = null, $description = null, $relatedaction = null, $mandatory = null, $type = null, $value = null) {
        $this->setName($name);
        $this->setDescription($description);
        $this->setRelatedAction($relatedaction);
        $this->setMandatory($mandatory);
        $this->setType($type);
        $this->setValue($value);
    }

    /**
     * Set Id.
     *
     * @param int $id
     * @return McollectiveLog
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set Agent Name.
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get Agent Name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set Agent Description.
     *
     * @param string $description
     * @return McollectiveAgent
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Get Agent Description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set Agent's related actions.
     *
     * @param McollectiveActions[]
     * @return McollectiveAgent
     */
    public function setRelatedAction($relatedaction) {
        $this->relatedAction = $relatedaction;
        return $this;
    }

    /**
     * Get Agent's related actions
     *
     * @return McollectiveAction[]
     */
    public function getRelatedAction() {
        return $this->relatedAction;
    }

    /**
     * Set if arg is required.
     *
     * @param boolean $required
     * @return McollectiveAgent
     */
    public function setMandatory($required) {
        $this->mandatory = $required == 'on' ? true : false;
        return $this;
    }

    /**
     * Get if arg is required.
     *
     * @return string
     */
    public function getMandatory() {
        return $this->mandatory;
    }

    /**
     * Set Arg type.
     *
     * @param string $type
     * @return McollectiveAgent
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Get arg type.
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }


    /**
     * Set arg value
     *
     * @param string $value
     * @return McollectiveAgent
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Get arg value
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

}
