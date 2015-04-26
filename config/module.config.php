<?php
// Awfull hack to tell to poedit to translate navigation labels
$translate = function ($message) { return $message; };
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
            // rewritten
            // 'mcollective_proxy_reply' => [
            //     'type' => 'Segment',
            //     'options' => [
            //         'route' => '/mcollective/proxy/reply',
            //         'defaults' => [
            //             '__NAMESPACE__' => 'KmbMcollective\Controller',
            //             'controller' => 'Reply',
            //             'action' => 'process',
            //             'envId' => '0',
            //         ],
            //     ],
            // ],
            'mcollective_proxy_reply' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/mcollective/proxy/reply',
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Reply',
                        'action' => 'newprocess',
                        'envId' => '0',
                    ],
                ],
            ],
            // replaced by newmcollective_history and mcollective_show
            // 'mcollective_history' => [
            //     'type' => 'Segment',
            //     'options' => [
            //         'route' => '[/env/:envId]/mcollective/history[/[:id]]',
            //         'constraints' => [
            //             'envId' => '[0-9]+',
            //             'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            //             'id' => '[a-fA-F0-9]+',
            //         ],
            //         'defaults' => [
            //             '__NAMESPACE__' => 'KmbMcollective\Controller',
            //             'controller' => 'Index',
            //             'action' => 'history',
            //             'envId' => '0',
            //             'id' => '0',
            //         ],
            //     ],

            // ],
            'mcollective_history' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '[/env/:envId]/mcollective/history',
                    'constraints' => [
                        'envId' => '[0-9]+',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-fA-F0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Index',
                        'action' => 'historyTable',
                        'envId' => '0',
                        'id' => '0',
                    ],
                ],
            ],
            'mcollective_show' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '[/env/:envId]/mcollective/history/:id',
                    'constraints' => [
                        'envId' => '[0-9]+',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-fA-F0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Index',
                        'action' => 'showDetail',
                        'envId' => '0',
                        'id' => '0',
                    ],
                ],
            ],
            'mcollective_metadatas' => [
                'type' => 'Segment',
                'options' => [
                    'verb' => 'get',
                    'route' => '[/env/:envId]/mcollective/metadata[/[:agent]]',
                    'constraints' => [
                        'envId' => '[0-9]+',
                        'agent' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Index',
                        'action' => 'metadata',
                        'envId' => '0',
                    ],
                ],
            ],
            // rewritten
            // 'mcollective_results' => [
            //     'type' => 'Segment',
            //     'options' => [
            //         'verb' => 'get',
            //         'route' => '/mcollective/results/:actionid[/requestid/:requestid]',
            //         'constraints' => [
            //             'actionid' => '[a-fA-F0-9]{32}',
            //             'requestid' => '[a-fA-F0-9]{32}',
            //         ],
            //         'defaults' => [
            //             '__NAMESPACE__' => 'KmbMcollective\Controller',
            //             'controller' => 'Result',
            //             'action' => 'getResults',
            //             'envId' => 0,
            //         ],
            //     ],
            // ],
            'mcollective_results' => [
                'type' => 'Segment',
                'options' => [
                    'verb' => 'get',
                    'route' => '/mcollective/results/:actionid[/requestid/:requestid]',
                    'constraints' => [
                        'actionid' => '[a-fA-F0-9]{32}',
                        'requestid' => '[a-fA-F0-9]{32}',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Result',
                        'action' => 'newgetResults',
                        'envId' => 0,
                    ],
                ],
            ],
            'mcollective_metadatas_update' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '[/env/:envId]/mcollective/metadata/:agent/update',
                    'constraints' => [
                        'envId' => '[0-9]+',
                        'agent' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'KmbMcollective\Controller',
                        'controller' => 'Index',
                        'action' => 'metadataUpdate',
                        'envId' => '0',
                    ],
                ],
            ],

        ],
    ],
    'navigation' => [
        'navbar' => [
            'mcollective' => [
                'label' => $translate('Mcollective'),
                'route' => 'mcollective',
                'tabindex' => 80,
                'pages' => [
                    [
                        'label' => $translate('Actions'),
                        'route' => 'mcollective',
                        'controller' => 'Index',
                        'action' => 'show',
                        'useRouteMatch' => true,
                        'tabindex' => 81,
                    ],
                    [
                        'label' => $translate('History'),
                        'route' => 'mcollective_history',
                        'controller' => 'Index',
                        'action' => 'history',
                        'useRouteMatch' => true,
                        'tabindex' => 82,
                    ],
                    [
                        'label' => $translate('Metadatas'),
                        'route' => 'mcollective_metadatas',
                        'controller' => 'Index',
                        'action' => 'metadata',
                        'useRouteMatch' => true,
                        'tabindex' => 83,
                    ],
                ],
            ],
        ],
        'breadcrumb' => [
            'home' => [
                'pages' => [
                    'mcollective' => [
                        'label' => $translate('Mcollective'),
                        'route' => 'mcollective',
                        'action' => 'show',
                        'useRouteMatch' => true,
                        'pages' => [
                            [
                                'label' => $translate('Actions'),
                                'route' => 'mcollective',
                                'controller' => 'Index',
                                'action' => 'show',
                                'useRouteMatch' => true,
                            ],
                            [
                                'label' => $translate('History'),
                                'route' => 'mcollective_history',
                                'controller' => 'Index',
                                'action' => 'history',
                                'useRouteMatch' => true,
                                'pages' => [
                                    [
                                        'id' => 'history',
                                        'label' => $translate('History'),
                                        'route' => 'mcollective_history',
                                        'controller' => 'Index',
                                        'action' => 'history',
                                        'useRouteMatch' => true,
                                    ],
                                ],
                            ],
                            [
                                'label' => $translate('Metadatas'),
                                'route' => 'mcollective_metadatas',
                                'controller' => 'Index',
                                'action' => 'metadata',
                                'useRouteMatch' => true,
                                'pages' => [
                                    [
                                        'id' => 'metadata',
                                        'label' => $translate('Metadatas'),
                                        'route' => 'mcollective_metadatas',
                                        'controller' => 'Index',
                                        'action' => 'metadata',
                                        'useRouteMatch' => true,
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'zenddb_repositories' => [
        // 'McollectiveLogRepository' => [
        //     'aggregate_root_class' => 'KmbMcollective\Model\McollectiveLog',
        //     'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveLogHydrator',
        //     'table_name' => 'mcollective_logs',
        //     'table_sequence_name' => 'mcollective_logs_id_seq',
        //     'repository_class' => 'KmbMcollective\Service\McollectiveLogRepository',
        // ],
        'ActionLogRepository' => [
            'aggregate_root_class' => 'KmbMcollective\Model\ActionLog',
            'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\ActionLogHydrator',
            'table_name' => 'action_logs',
            'table_id' => 'actionid',
            'table_sequence_name' => 'action_logs_id_seq',
            'command_log_table' => 'command_logs',
            'command_log_sequence_name' => 'command_logs_id_seq',
            'command_log_repository' => 'KmbMcollective\Service\CommandLogRepository',
            'command_log_hydrator' => 'KmbMcollective\Hydrator\CommandLogHydrator',
            'command_log_class' => 'KmbMcollective\Model\CommandLog',
            'repository_class' => 'KmbMcollective\Service\ActionLogRepository',
            'factory' => 'KmbMcollective\Service\ActionLogRepositoryFactory'
        ],
        'CommandLogRepository' => [
            'aggregate_root_class' => 'KmbMcollective\Model\CommandLog',
            'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\CommandLogHydrator',
            'table_name' => 'command_logs',
            'table_id' => 'requestid',
            'table_sequence_name' => 'command_logs_id_seq',
            'reply_class' => 'KmbMcollective\Model\CommandReply',
            'reply_repository' => 'KmbMcollective\Service\CommandReplyRepository',
            'reply_hydrator_class' => 'KmbMcollective\Hydrator\CommandReplyHydrator',
            'reply_table_name' => 'command_reply_logs',
            'reply_table_sequence_name' => 'command_reply_id_seq',
            'factory' => 'KmbMcollective\Service\CommandLogRepositoryFactory',
            'repository_class' => 'KmbMcollective\Service\CommandLogRepository',
        ],
        'CommandReplyRepository' => [
            'aggregate_root_class' => 'KmbMcollective\Model\CommandReply',
            'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\CommandReplyHydrator',
            'table_name' => 'command_reply_logs',
            'table_sequence_name' => 'command_reply_logs_id_seq',
            'repository_class' => 'KmbMcollective\Service\CommandReplyRepository',
        ],
        // 'McollectiveHistoryRepository' => [
        //     'aggregate_root_class' => 'KmbMcollective\Model\McollectiveHistory',
        //     'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveHistoryHydrator',
        //     'table_name' => 'mcollective_actions_logs',
        //     'table_sequence_name' => 'mcollective_actions_logs_id_seq',
        //     'log_class' => 'KmbMcollective\Model\McollectiveLog',
        //     'log_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveLogHydrator',
        //     'log_table_name' => 'mcollective_logs',
        //     'log_table_sequence_name' => 'mcollective_log_id_seq',
        //     'factory' => 'KmbMcollective\Service\McollectiveHistoryRepositoryFactory',
        //     'repository_class' => 'KmbMcollective\Service\McollectiveHistoryRepository',
        // ],
        'McollectiveAgentRepository' => [
            'aggregate_root_class' => 'KmbMcollective\Model\McollectiveAgent',
            'aggregate_root_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveAgentHydrator',
            'table_name' => 'mcollective_agents_metadata',
            'table_sequence_name' => 'mcollective_agents_metadata_id_seq',
            'action_class' => 'KmbMcollective\Model\McollectiveAction',
            'action_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveActionHydrator',
            'action_table_name' => 'mcollective_actions_metadata',
            'action_table_sequence_name' => 'mcollective_actions_metadata_id_seq',
            'argument_class' => 'KmbMcollective\Model\McollectiveArgument',
            'argument_hydrator_class' => 'KmbMcollective\Hydrator\McollectiveArgumentHydrator',
            'argument_table_name' => 'mcollective_actions_arguments_metadata',
            'argument_table_sequence_name' => 'mcollective_actions_arguments_metadata_id_seq',
            'factory' => 'KmbMcollective\Service\McollectiveAgentRepositoryFactory',
            'repository_class' => 'KmbMcollective\Service\McollectiveAgentRepository',
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
            'KmbMcollective\Controller\Index' => 'KmbMcollective\Controller\IndexController',
            'KmbMcollective\Controller\Result' => 'KmbMcollective\Controller\ResultController',
            'KmbMcollective\Controller\Reply' => 'KmbMcollective\Controller\ReplyController'
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
                ],
                [
                    'controller' => 'KmbMcollective\Controller\Index',
                    'actions' => ['metadata','metadataUpdate'],
                    'roles' => ['admin']
                ]
            ]
        ],
    ],
    'datatables' => [
        // 'historyDatatable' => [
        //     'id' => 'mcollective_logs',
        //     'classes' => ['table', 'table-striped', 'table-hover', 'table-condensed', 'bootstrap-datatable'],
        //     'collectorFactory' => 'KmbMcollective\Service\McollectiveHistoryCollectorFactory',
        //     'columns' => [
        //         [
        //             'decorator' => 'KmbMcollective\View\Decorator\SourceDecorator',
        //         ],
        //         [
        //             'decorator' => 'KmbMcollective\View\Decorator\FullNameDecorator',
        //             'key'       => 'fullname',
        //         ],
        //         [
        //             'decorator' => 'KmbMcollective\View\Decorator\AgentDecorator',
    	// 	    'key'       => 'agent',
        //         ],
        //         [
        //             'decorator' => 'KmbMcollective\View\Decorator\SummaryDecorator',
        //         ],
        //         [
        //             'decorator' => 'KmbMcollective\View\Decorator\ServersDecorator',
        //         ],
        //         [
        //             'decorator' => 'KmbMcollective\View\Decorator\TimeDecorator',
        //             'key'       => 'received_at',
        //         ],
        //     ]
        // ],
        'ActionDatatable' => [
            'id' => 'action_logs',
            'classes' => ['table', 'table-striped', 'table-hover', 'table-condensed', 'bootstrap-datatable'],
            'collectorFactory' => 'KmbMcollective\Service\ActionCollectorFactory',
            'columns' => [
                [
                    'decorator' => 'KmbMcollective\View\Decorator\NewSourceDecorator',
                ],
                [
                    'decorator' => 'KmbMcollective\View\Decorator\NewFullNameDecorator',
                    'key'       => 'fullname',
                ],
                [
                    'decorator' => 'KmbMcollective\View\Decorator\NewSummaryDecorator',
                ],
                [
                    'decorator' => 'KmbMcollective\View\Decorator\ReplyDecorator',
                ],
                [
                    'decorator' => 'KmbMcollective\View\Decorator\NewTimeDecorator',
                    'key' => 'created_at',
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
        // 'invokables' => [
        //     'KmbMcollective\Service\ReplyHandler' => 'KmbMcollective\Service\ReplyHandler',
        // ],
        'factories' => [
            'KmbMcollective\Service\McollectiveHistory' => 'KmbMcollective\Service\McollectiveHistoryFactory',
            'ReplyHandler' => 'KmbMcollective\Service\ReplyHandlerFactory',
        ],
        'abstract_factories' => [
            'KmbMcollective\Service\AbstractHandlerFactory',
        ],
    ],
];
