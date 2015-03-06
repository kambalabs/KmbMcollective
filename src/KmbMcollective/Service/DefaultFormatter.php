<?php
/**
 * @copyright Copyright (c) 2014, 2015 Orange Applications for Business
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
namespace KmbMcollective\Service;

use KmbMcollective\View\AbstractFormatter;

class DefaultFormatter extends AbstractFormatter
{
    protected $agentRepository;

    public function __construct($agentRepository){
        $this->setAgentRepository($agentRepository);
    }

    public function format($object){
        $agent = $this->agentRepository->getByName($object->getAgent());
        $actionName = $object->getAction();
        if(isset($agent)) {
            $action = array_values(array_filter($agent->getRelatedActions(),function($action) use ($actionName) {
                        if($action->getName() == $actionName) {
                            return true;
                        }
                    }));
            if(! empty($action)) {
                $summary = [ 'detail' => $action[0]->getShortDesc(), 'icon' => $action[0]->getIhmIcon() ];
            }else{
                $summary = [ 'detail' => 'unfinished action', 'icon' => '' ];
            }


            if($object->getIhmLog() !== null) {
                $params = json_decode($object->getIhmLog()[0]->getParameters());

                if(!empty($params)) {
                    foreach($params as $arg => $value) {
                        $summary['detail'] = str_replace('#'.$arg.'#', $value,$summary['detail']);
                    }
                }
            }
            $object->setSummary($summary);
        }else{
            error_log("Agent '" .$agent."' not found..");
        }
        return $object;
    }

    public function setAgentRepository($repo){
        $this->agentRepository = $repo;
        return $this;
    }
}
