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

use KmbMcollective\Model\McollectiveHistoryInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class McollectiveHistoryHydrator implements HydratorInterface
{
    /**
     * Extract values from an object
     *
     * @param  UserInterface $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'actionid' => $object->getActionId(),
            'requestid' => $object->getRequestId(),
            'caller' => $object->getCaller(),
            'hostname' => $object->getHostname(),
            'agent' => $object->getAgent(),
            'action' => $object->getAction(),
            'sender' => $object->getSender(),
            'statuscode' => $object->getStatusCode(),
            'result' => $object->getResult(),
            'type' => $object->getType(),
            'finished' => $object->isFinished() ? true : false,
            'received_at' => $object->getReceivedAt(),
        ];
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  UserInterface $object
     * @return UserInterface
     */
    public function hydrate(array $data, $object)
    {
        $object->setId($data['id']);
        $object->setActionid($data['actionid']);
        $object->setRequestId($data['requestid']);
        $object->setCaller($data['caller']);
        $object->setHostname($data['hostname']);
        $object->setAgent($data['agent']);
        $object->setAction($data['action']);
        $object->setSender($data['sender']);
        $object->setStatusCode($data['statuscode']);
        $object->setType($data['type']);
        $object->setResult($data['result']);
        if($data['finished']) {
            $object->setFinished();
        }
        $object->setReceivedAt($data['received_at']);
        return $object;
    }
}
