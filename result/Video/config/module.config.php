<?php
{'Settings': ''}

return array(


    'router' => array(
        'routes' => array(
            'myTexts' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user/texts[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Texsts\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'texsts_entity' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Texsts/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Texsts\Entity' => 'texsts_entity',
                )
            ),
        )
    ),




    'controllers' => array(
        'invokables' => array(
            'Texsts\Controller\Index' => 'Texsts\Controller\IndexController',
        ),
    ),
);