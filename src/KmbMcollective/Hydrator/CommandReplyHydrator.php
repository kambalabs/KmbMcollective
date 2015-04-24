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
namespace KmbMcollective\Hydrator;

use KmbMcollective\Model\CommandReplyInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class CommandReplyHydrator implements HydratorInterface
{
    public function extract($object)
    {
        $data = [
            'hostname' => $object->getHostname(),
            'username' => $object->getUser(),
            'requestid' => $object->getRequestId(),
            'agent' => $object->getAgent(),
            'action' => $object->getAction(),
            'statuscode' => $object->getStatusCode(),
            'result' => $object->getResult(),
            'finished' => $object->is_finished()
        ];
        if($object->getId()){
            $data['id'] = $object->getId();
        }
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        $object->setId($this->getData('id', $data));
        $object->setHostname($this->getData('hostname',$data));
        $object->setUser($this->getData('username',$data));
        $object->setRequestId($this->getData('requestid',$data));
        $object->setAgent($this->getData('agent',$data));
        $object->setAction($this->getData('action',$data));
        $object->setResult($this->getData('result',$data));
        $object->setStatusCode($this->getData('statuscode',$data));
        $object->finish($this->getData('finished',$data));
        return $object;
    }

    protected function getData($key, $data)
    {
        if (isset($data['r.' . $key])) {
            return $data['r.' . $key];
        }
        if (isset($data[$key])) {
            return $data[$key];
        }
        return null;
    }

}
