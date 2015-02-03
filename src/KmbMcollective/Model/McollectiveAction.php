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

class McollectiveAction implements McollectiveActionInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var McollectiveAgent */
    protected $relatedAgent;

    protected $arguments = [];

    protected $longDetail;

    protected $shortDetail;

    protected $ihmIcon;

    protected $limitNum;

    protected $limitHost;

        /**
     * @param string   $name
     * @param string   $description
     * @param McollectiveAction[]   $relatedActions
     */
    public function __construct($name = null, $description = null, $relatedagent = null, $longdetail = null, $shortdetail = null, $ihmicon = null, $limitnum = null, $limithost = []) {
        $this->setName($name);
        $this->setDescription($description);
        $this->setRelatedAgent($relatedagent);
        $this->setLongDesc($longdetail);
        $this->setShortDesc($shortdetail);
        $this->setIhmIcon($ihmicon);
        $this->setLimitNumber($limitnum);
        $this->setLimitHosts($limithost);
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
     * Set Action Name.
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get Action Name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set Action Description.
     *
     * @param string $description
     * @return McollectiveAgent
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Get Action Description.
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
    public function setRelatedAgent($relatedagent) {
        $this->relatedAgent = $relatedagent;
        return $this;
    }


    /**
     * Get Agent's related actions
     *
     * @return McollectiveAction[]
     */
    public function getRelatedAgent() {
        return $this->relatedAgent;
    }



    /**
     * Set Agent's related actions.
     *
     * @param McollectiveActions[]
     * @return McollectiveAgent
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * Get Agent's related actions
     *
     * @return McollectiveAction[]
     */
    public function getArguments($name = null)
    {
        if( $name == null) {
            return $this->arguments;
        } else {
            if($this->hasArguments()){
                foreach($this->arguments as $argument) {
                    if($argument->getName() == $name) {
                        return $argument;
                    }
                }
            }
        }
        return null;
    }



    /**
     * Add an agent related action.
     *
     * @param McollectiveActionInterface $relatedAction
     * @return McollectiveAgentInterface
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
        return $this;
    }

    /**
     * Add action's arguments.
     *
     * @param McollectiveActionInterface $relatedAction
     * @return McollectiveAgentInterface
     */
    public function hasArguments()
    {
        return !empty($this->arguments);
    }




    /**
     * Set Action long description.
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setLongDesc($longdesc) {
        $this->longDetail = $longdesc;
        return $this;
    }


    /**
     * Get Action long description.
     *
     * @return string
     */
    public function getLongDesc() {
        return $this->longDetail;
    }

    /**
     * Set Action short description.
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setShortDesc($shortdesc) {
        $this->shortDetail = $shortdesc;
        return $this;
    }

    /**
     * Get Action short description.
     *
     * @return string
     */
    public function getShortDesc() {
        return $this->shortDetail;
    }


    /**
     * Set Agent ihm icon
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setIhmIcon($icon) {
        $this->ihmIcon = $icon;
        return $this;
    }

    /**
     * Get Agent ihm icon
     *
     * @return string
     */
    public function getIhmIcon() {
        return $this->ihmIcon;
    }

    /**
     * Set Action number of host impacted
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setLimitNumber($max) {
        $this->limitNum = $max;
        return $this;
    }

    /**
     * Get Agent max number of host impacted
     *
     * @return string
     */
    public function getLimitNumber() {
        return $this->limitNum;
    }


    /**
     * Set Action's allowed host
     *
     * @param string $name
     * @return McollectiveAgent
     */
    public function setLimitHosts($hosts) {
        $this->limitHost = $hosts;
        return $this;
    }

    /**
     * Get Agent max number of host impacted
     *
     * @return string
     */
    public function getLimitHosts() {
        return $this->limitHost;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

}
