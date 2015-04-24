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

class CommandLog implements CommandLogInterface
{

    protected $requestId;
    protected $actionId;
    protected $nodes = [];
    protected $replies = [];

    public function setId($requestid){
        $this->requestId = $requestid;
        return $this;
    }
    public function getId(){
        return $this->requestId;
    }

    public function setActionId($actionid){
        $this->actionId = $actionid;
        return $this;
    }

    public function getActionId(){
        return $this->actionId;
    }
    public function setNodes($nodes){
        $this->nodes = $nodes;
        return $this;
    }
    public function addNodes($node){
        $this->nodes[] = $node;
        return $this;
    }
    public function getNodes(){
        return $this->nodes;
    }

    public function setReplies($replies){
        $this->replies = $replies;
        return $this;
    }

    public function getReplies(){
        return $this->replies;
    }

    public function getReplyFor($host){
        foreach($this->getReplies() as $reply) {
            if($reply->getHostname() == $host){
                return $reply;
            }
        }
        return;
    }

    public function getAllFinishedReplies(){
        $replies = [];
        foreach($this->getReplies() as $reply) {
            if($reply->is_finished()){
                $replies[] = $reply;
            }
        }
        return $replies;
    }

    public function addReply($reply){
        $this->replies[] = $reply;
        return $this;
    }

    public function __construct($id = null){
        $this->setId($id);
        return $this;
    }

    public function hasReplies(){
        return sizeof($this->replies) > 0;
    }
}
