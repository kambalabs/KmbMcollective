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
namespace KmbMcollective\Service;

use KmbPermission\Service\EnvironmentInterface;
use KmbPuppetDb\Query\EnvironmentsQueryBuilderInterface;
use KmbPuppetDb\Service;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class McollectiveHistoryCollectorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new McollectiveHistoryCollector();

        try {
        $historyRepositoryService = $serviceLocator->get('KmbMcollective\Service\McollectiveHistory');
        $agentRepository = $serviceLocator->get('McollectiveAgentRepository');
        $userRepository = $serviceLocator->get('UserRepository');
        $metadatas = $serviceLocator->get('Config')['mcollective']['translation'];
        $viewHelperManager = $serviceLocator->get('ViewHelperManager');
        } catch (\Exception $e) {
            do {
                var_dump($e->getMessage());
            } while ($e = $e->getPrevious());
        }

        $service->setHistoryRepositoryService($historyRepositoryService);
        $service->setAgentRepository($agentRepository);
        $service->setSpecificMetadata($metadatas);
        $service->setUserRepository($userRepository);
        $service->setViewHelperManager($viewHelperManager);

        return $service;
    }
}
