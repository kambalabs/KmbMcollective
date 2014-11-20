<?php
return [
    'router' => [
        'routes' => [
            'mcollective' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '[/env/:envId]/mcollective[/[:action]]',
                    'constraints' => [
                        'envId' => '[0-9]+',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Index',
                        'action' => 'show',
                        'envId' => '0',
                    ],
                ],		
            ],
            'mcollective_history' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '[/env/:envId]/mcollective/history[/[:id]]',
                    'constraints' => [
                        'envId' => '[0-9]+',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-fA-F0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Index',
                        'action' => 'history',
                        'envId' => '0',
			'id' => '0',
                    ],
                ],
            
            ],

        ],
    ],
    'zenddb_repositories' => [
        'McollectiveLogRepository' => [
            'aggregate_root_class' => 'KmbMcollective\Model\McollectiveLog',
            'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveLogHydrator',
            'table_name' => 'mcollective_logs',
            'table_sequence_name' => 'mcollective_logs_id_seq',
            'repository_class' => 'KmbMcollective\Service\McollectiveLogRepository',
            ],
	 'McollectiveHistoryRepository' => [
            'aggregate_root_class' => 'KmbMcollective\Model\McollectiveHistory',
            'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveHistoryHydrator',
            'table_name' => 'mcollective_actions_logs',
            'table_sequence_name' => 'mcollective_actions_logs_id_seq',
            'repository_class' => 'KmbMcollective\Service\McollectiveHistoryRepository',
        ],

    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'KmbMcollective\Controller\Index' => 'KmbMcollective\Controller\IndexController'
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'zfc_rbac' => [
        'guards' => [
            'ZfcRbac\Guard\ControllerGuard' => [
                [
                    'controller' => 'KmbMcollective\Controller\Index',
                    'actions' => ['show', 'agents', 'run', 'logs', 'history'],
                    'roles' => ['user']
                ]
            ]
        ],
    ],
    'datatables' => [
        'historyDatatable' => [
            'id' => 'mcollective_logs',
            'classes' => ['table', 'table-striped', 'table-hover', 'table-condensed', 'bootstrap-datatable'],
            'collectorFactory' => 'KmbMcollective\Service\McollectiveLogCollectorFactory',
            'columns' => [
                [
                    'decorator' => 'KmbMcollective\View\Decorator\FullNameDecorator',
		    'key'       => 'fullname',
                ],
                [
                    'decorator' => 'KmbMcollective\View\Decorator\AgentDecorator',
    		    'key'       => 'agent',	
                ],
                [
                    'decorator' => 'KmbMcollective\View\Decorator\FilterDecorator',
		    'key'       => 'filter',
                ],
		[
                    'decorator' => 'KmbMcollective\View\Decorator\ServersDecorator',
                ],  
		[
                    'decorator' => 'KmbMcollective\View\Decorator\TimeDecorator',
		    'key'       => 'received_at',
                ],
            ]
        ]
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'KmbMcollective\Service\McollectiveLog' => 'KmbMcollective\Service\McollectiveLogFactory',
        ],
    ],
];
