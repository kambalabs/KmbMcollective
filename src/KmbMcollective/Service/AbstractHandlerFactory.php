<?php
/**
 * @copyright Copyright (c) 2014 Orange Applications for Business
 * @link      http://github.com/multimediabs/kamba for the canonical source repository
 *
 * This file is part of kamba.
 *
 * kamba is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * kamba is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with kamba.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace KmbMcollective\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractHandlerFactory implements AbstractFactoryInterface
{

    /**
     * @var array
     */
    protected $config;

    protected $configKey = 'mcollective';
    protected $configHandler = 'handler';

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator);
        if (empty($config)) {
            return false;
        }

        return (isset($config[$this->configHandler][$requestedName]) && is_array($config[$this->configHandler][$requestedName]));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     * @return mixed
     * @throws MissingConfigurationException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        error_log("Calling createServiceWithName : ".$requestedName);
        $config = $this->getConfig($serviceLocator);
        $config = $config[$this->configHandler][$requestedName];


        /** @var FactoryInterface $collectorFactory */
        $replyHandlerFactory = new $config['factory'];

        // $datatable = new DataTable(isset($config['id']) ? $config['id'] : $requestedName);
        // $datatable->setCollector($collectorFactory->createService($serviceLocator));
        // $columns = array();
        // foreach ($config['columns'] as $columnConfig) {
        //     $columns[] = Column::factory($serviceLocator, $columnConfig);
        // }
        // $datatable->setColumns($columns);
        // if (isset($config['classes'])) {
        //     $datatable->setClasses($config['classes']);
        // }

        // return $datatable;

        return $replyHandlerFactory->createService($serviceLocator);
    }

    protected function getConfig(ServiceLocatorInterface $serviceLocator)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$serviceLocator->has('Config')) {
            $this->config = array();
            return $this->config;
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config[$this->configKey])) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}
