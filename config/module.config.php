<?php
use StatusAware\Service\StatusManager;
use StatusAware\ServiceManagerAwareInterface\StatusInterface;
return array(
    'controllers' => array(
        'invokables' => array(
            'StatusAware\Controller\Status' => 'StatusAware\Controller\StatusController'
        )
    ),
    'router' => array(
        'routes' => array(
            'status-aware' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/status',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'StatusAware\Controller',
                        'controller' => 'Status',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'StatusAwareManager' => function ($serviceManager) {
                $statusManager = new StatusManager();
                return $statusManager;
            }
        ),
        'initializers' => array(
            'ServiceStatusInterface' => function ($model, $serviceManager) {
                if ($model instanceof StatusInterface) {
                    /**
                     *
                     * @var $statusManager StatusManager
                     */
                    $statusManager = $serviceManager->get('StatusAwareManager');
                    if ($statusManager instanceof StatusManager) {
                        $statusManager->addStatusReportingClass($model);
                        
                    }
                }
            }
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'StatusAware' => __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    )
);
