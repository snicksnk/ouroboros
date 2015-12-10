<?php
namespace {{module_name}};


class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }


    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                '{{module_name}}\Repository\Text' => function ($em) {
                    $em = $em->get('Doctrine\ORM\EntityManager');
                    $svc = new \{{module_name}}\Repository\TextRepository($em);
                    return $svc;
                },
                '{{module_name}}\Service\{{module_name}}' => function ($sm) {

                    $rep = $sm->get('Texsts\Repository\{{module_name}}');
                    $svc = new \{{module_name}}\Service\{{module_name}}();
                    $svc->setRepository($rep);

                    return $svc;
                },
                '{{module_name}}\Form\{{module_name}}' => function($em) {
                    return new \{{module_name}}\Form\{{module_name}}();
                }
            ),
        );
    }
}
