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

use KmbMcollective\Model\CommandLogInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class CommandLogHydrator implements HydratorInterface
{
    public function extract($object)
    {
        $data = [
            'requestid' => $object->getId(),
            'actionid' => $object->getActionId(),
        ];
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        $object->setId($this->getData('requestid', $data));
        $object->setActionId($this->getData('actionid',$data));
        return $object;
    }

    protected function getData($key, $data)
    {
        if (isset($data['c.' . $key])) {
            return $data['c.' . $key];
        }
        if (isset($data[$key])) {
            return $data[$key];
        }
        return null;
    }

}
