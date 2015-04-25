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

class ActionLog implements ActionLogInterface
{
    protected $actionid;
    protected $environment;
    protected $parameters;
    protected $description;
    protected $login;
    protected $fullname;
    protected $finished;
    protected $createdAt;
    protected $commands = [];
    protected $ihmIcon;


    public function getId(){
        return $this->actionid;
    }

    public function setId($id){
        $this->actionid = $id;
        return $this;
    }

    public function getParameters(){
        return $this->parameters;
    }

    public function setParameters($params){
        $this->parameters = $params;
        return $this;
    }

    public function setIhmIcon($icon){
        $this->ihmIcon = $icon;
        return $this;
    }
    public function getIhmIcon(){
        return $this->ihmIcon;
    }
    public function getDescription(){
        return $this->description;
    }
    public function setDescription($description){
        $this->description = $description;
        return $this;
    }
    public function getLogin(){
        return $this->login;
    }
    public function setLogin($login){
        $this->login = $login;
        return $this;
    }
    public function getFullName(){
        return $this->fullname;
    }
    public function setFullName($fullname){
        $this->fullname = $fullname;
        return $this;
    }
    public function is_finished(){
        return $this->finished;
    }
    public function finish($finished){
        $this->finished = $finished;
        return $this;
    }

    public function setCommands($cmds){
        $this->commands = $cmds;
        return $this;
    }

    public function addCommand($cmd){
        $cmd->setActionId($this->actionid);
        $this->commands[] = $cmd;
        return $this;
    }

    public function setEnvironment($env)
    {
        $this->environment = $env;
        return $this;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getCommands(){
        return $this->commands;
    }

    public function hasCommands(){
        return sizeof($this->commands) > 0;
    }

    public function getCommandById($id){
        foreach($this->getCommands() as $cmd){
            if ($cmd->getId() == $id) {
                return $cmd;
            }
        }
        return null;
    }

    public function setCreationDate($date)
    {
        $this->createdAt = $date;
        return $this;
    }

    public function getCreationDate()
    {
        return $this->createdAt;
    }


    public function getByRequestId($id)
    {
        foreach($this->getCommands() as $command)
        {
            if($command->getId() == $id)
            {
                return $command;
            }
        }
        return;
    }

    public function getServerReplyCount(){
        $servers = [];
        foreach($this->getCommands() as $command){
            foreach($command->getReplies() as $reply){
                if(! in_array($reply->getHostname(), $servers)){
                    $servers[] = $reply->getHostname();
                }
            }
        }
        return count($servers);
    }

    public function getGlobalStatus(){
        $status = '';
        $errorCount = 0;
        foreach($this->getCommands() as $command){
            foreach($command->getReplies() as $reply){
                if($reply->getStatusCode() != 0){
                    $errorCount++;
                    if($status === 'success'){
                        $status = 'partial';
                    }elseif($status === ''){
                        $status = 'error';
                    }
                }else{
                    if($status === 'error'){
                        $status = 'partial';
                    }elseif( $status === ''){
                        $status = 'success';
                    }
                }
            }
        }
        return ['status' => $status, 'errors' => $errorCount];
    }

    public function getResultByHost(){
        $result = [];
        foreach($this->getCommands() as $command){
            foreach($command->getReplies() as $reply){
                $result[$reply->getHostname()][] = $reply->toArray();
            }
        }
        return $result;
    }

    public function __construct($actionid = null){
        $this->setId($actionid);
        if(!isset($this->createdAt)){
            $this->setCreationDate(date('Y-m-d H:i:s'));
        }
        $this->finish(false);
    }
}
