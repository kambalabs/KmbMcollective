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

use KmbMcollective\Model\ActionLogInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class ActionLogHydrator implements HydratorInterface
{
    public function extract($object)
    {
        $data =[
            'actionid' => $object->getId(),
            'environment' => $object->getEnvironment(),
            'parameters' => $object->getParameters(),
            'description' => $object->getDescription(),
            'login' => $object->getLogin(),
            'fullname' => $object->getFullName(),
            'created_at' => $object->getCreationDate(),
            'finished' => $object->is_finished(),
            'ihm_icon' => $object->getIhmIcon(),
            'source' => $object->getSource(),
        ];
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        $object->setId($this->getData('actionid', $data));
        $object->setEnvironment($this->getData('environment', $data));
        $object->setParameters($this->getData('parameters', $data));
        $object->setDescription($this->getData('description', $data));
        $object->setLogin($this->getData('login', $data));
        $object->setFullName($this->getData('fullname', $data));
        $object->finish($this->getData('finished',$data));
        $object->setCreationDate($this->getData('created_at',$data));
        $object->setIhmIcon($this->getData('ihm_icon',$data));
        $object->setSource($this->getData('source',$data));
        return $object;
    }

    protected function getData($key, $data)
    {
        if (isset($data['g.' . $key])) {
            return $data['g.' . $key];
        }
        if (isset($data[$key])) {
            return $data[$key];
        }
        return null;
    }

}
