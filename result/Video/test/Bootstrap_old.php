<?php

namespace TexstsTest;

use Application\Lib\YeopenController;
use Texsts\Controller\IndexController;
use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        $zf2ModulePaths = array(dirname(dirname(__DIR__)));
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $config = array(
            'module_listener_options' => array(
                'module_paths' => $zf2ModulePaths,
            ),
            'modules' => array(
                'DoctrineModule',
                'DoctrineORMModule'
            )
        );

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    public static function chroot()
    {
        $rootPath = dirname(static::findParentPath('module'));
        chdir($rootPath);
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getAppConfigPath()
    {
        $path = realpath(self::findParentPath('vendor').'/../config/application.config.php');
        return $path;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');


        if (class_exists('Zend\Loader\AutoloaderFactory')) {
            return;
        }

        $zf2Path = false;

        $loader = include $vendorPath.'/autoload.php';


        if (getenv('ZF2_PATH')) {            // Support for ZF2_PATH environment variable
            $zf2Path = getenv('ZF2_PATH');
        } elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
            $zf2Path = get_cfg_var('zf2_path');
        }



        if ($zf2Path) {
            if (isset($loader)) {

                $loader->add('Zend', $zf2Path);
                $loader->add('ZendXml', $zf2Path);
            } else {

                include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
                Zend\Loader\AutoloaderFactory::factory(array(
                    'Zend\Loader\StandardAutoloader' => array(
                        'autoregister_zf' => true
                    )
                ));
            }
        }

        if (!class_exists('Zend\Loader\AutoloaderFactory')) {
            throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
        }



    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}



Bootstrap::init();
Bootstrap::chroot();