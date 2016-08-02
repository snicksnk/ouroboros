<?php
namespace Settings;


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
                'Settings\Repository\Text' => function ($em) {
                    $em = $em->get('Doctrine\ORM\EntityManager');
                    $svc = new \Settings\Repository\TextRepository($em);
                    return $svc;
                },
                'Settings\Service\Settings' => function ($sm) {

                    $rep = $sm->get('Texsts\Repository\Settings');
                    $svc = new \Settings\Service\Settings();
                    $svc->setRepository($rep);

                    return $svc;
                },
                'Settings\Form\Settings' => function($em) {
                    return new \Settings\Form\Settings();
                }
            ),
        );
    }
}