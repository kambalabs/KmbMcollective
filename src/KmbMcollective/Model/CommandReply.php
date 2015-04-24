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

class CommandReply implements CommandReplyInterface
{
    protected $id;
    protected $host;
    protected $user;
    protected $statuscode;
    protected $result;
    protected $requestid;
    protected $agent;
    protected $action;
    protected $finished = false;


    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function finish($bool){
        $this->finished = $bool;
        return $this;
    }

    public function is_finished(){
        return $this->finished;
    }

    public function getHostname(){
        return $this->host;
    }

    public function setHostname($hostname){
        $this->host = $hostname;
        return $this;
    }

    public function setStatusCode($code){
        $this->statuscode = $code;
        return $this;
    }

    public function getStatusCode(){
        return $this->statuscode;
    }


    public function setResult($result){
        $this->result = $result;
        return $this;
    }

    public function getResult(){
        return $this->result;
    }

    public function getRequestId(){
        return $this->requestid;
    }

    public function setRequestId($requestId){
        $this->requestid = $requestId;
        return $this;
    }

    public function setUser($caller){
        $this->user = $caller;
        return $this;
    }

    public function getUser(){
        return $this->user;
    }

    public function getAgent(){
        return $this->agent;
    }

    public function setAgent($agent){
        $this->agent = $agent;
        return $this;
    }

    public function getAction(){
        return $this->action;
    }

    public function setAction($action){
        $this->action = $action;
        return $this;
    }

    public function toArray(){
        return [
            'requestid' => $this->getRequestid(),
            'caller' => $this->getUser(),
            'hostname' => $this->getHostname(),
            'agent' => $this->getAgent(),
            'action' => $this->getAction(),
                //            'sender' => $this->getSender(),
            'statuscode' => $this->getStatusCode(),
            'result' => $this->getResult(),
                //            'received_at' => $this->getReceivedAt(),
            'finished' => $this->is_finished(),
        ];
    }

    public function __construct($id = null){
        $this->setId($id);
        return $this;
    }
}
