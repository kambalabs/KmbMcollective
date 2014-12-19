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

use KmbMcollective\Model\McollectiveLogInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class McollectiveLogHydrator implements HydratorInterface
{
    /**
     * Extract values from an object
     *
     * @param  McollectiveLogInterface $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'actionid'    => $object->getActionId(),
            'login'       => $object->getLogin(),
            'fullname'    => $object->getFullName(),
            'agent'       => $object->getAgent(),
            'filter'      => $object->getFilter(),
            'pf'          => $object->getPf(),
            'parameters'  => $object->getParameters(),
            'received_at' => $object->getReceivedAt(),
        ];
        if ($object->getId() != null) {
            $data['id'] = $object->getId();
        }
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array                   $data
     * @param  McollectiveLogInterface $object
     * @return McollectiveLogInterface
     */
    public function hydrate(array $data, $object)
    {
        $object->setId($data['id']);
        $object->setActionid($data['actionid']);
        $object->setLogin($data['login']);
        $object->setFullName($data['fullname']);
        $object->setAgent($data['agent']);
        $object->setFilter($data['filter']);
        $object->setPf($data['pf']);
        $object->setParameters($data['parameters']);
        $object->setReceivedAt($data['received_at']);
        return $object;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array                   $data
     * @param  McollectiveLogInterface $object
     * @return McollectiveLogInterface
     */
    public function hydrateFromJoin(array $data, $object)
    {
        $object->setId($data['log.id']);
        $object->setActionid($data['log.actionid']);
        $object->setLogin($data['log.login']);
        $object->setFullName($data['log.fullname']);
        $object->setAgent($data['log.agent']);
        $object->setFilter($data['log.filter']);
        $object->setPf($data['log.pf']);
        $object->setParameters($data['log.parameters']);
        $object->setReceivedAt($data['received_at']);
        return $object;
    }
}
