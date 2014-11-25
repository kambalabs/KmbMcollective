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

use KmbMcollective\Model\McollectiveActionInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class McollectiveActionHydrator implements HydratorInterface
{
    /**
     * Extract values from an object
     *
     * @param  McollectiveAgentInterface $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
//            'agent_id' => $object->getRelatedAgent();
            'id'   => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'long_detail' => $object->getLongDesc(),
            'short_detail' => $object->getShortDesc(),
            'ihm_icon' => $object->getIhmIcon(),
            'limit_number' => $object->getLimitNumber(),
            'limit_host' => $object->getLimitHosts(),
        ];
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  McollectiveAgentInterface $object
     * @return McollectiveAgentInterface
     */
    public function hydrate(array $data, $object)
    {
        error_log("Data : " . print_r($data,true));
        $object->setId($data['mact.id']);
        $object->setName($data['mact.name']);
        $object->setDescription($data['mact.description']);
        $object->setLongDesc($data['mact.long_detail']);
        $object->setShortDesc($data['mact.short_detail']);
        $object->setIhmIcon($data['mact.ihm_icon']);
        $object->setLimitNumber($data['mact.limit_number']);
        $object->setLimitHosts($data['mact.limit_host']);
        return $object;
    }
}
