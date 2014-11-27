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

use KmbMcollective\Model\McollectiveArgumentInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class McollectiveArgumentHydrator implements HydratorInterface
{
    /**
     * Extract values from an object
     *
     * @param  McollectiveArgumentInterface $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'name' => $object->getName(),
            'action_id' => $object->getRelatedAction() != null ? $object->getRelatedAction()->getId() : null,
            'description' => $object->getDescription(),
            'mandatory' => $object->getMandatory(),
            'type' => $object->getType(),
            'value' => $object->getValue(),
        ];
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  McollectiveArgumentInterface $object
     * @return McollectiveArgumentInterface
     */
    public function hydrate(array $data, $object)
    {
        $object->setId($data['margs.id']);
        $object->setName($data['margs.name']);
        $object->setDescription($data['margs.description']);
        $object->setMandatory($data['margs.mandatory']);
        $object->setType($data['margs.type']);
        $object->setValue($data['margs.value']);
        return $object;
    }
}
