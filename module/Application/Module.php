<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\EventManager\Event;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->getSharedManager()->attach('Application\Controller\IndexController', 'indexAction', function (Event $e) {
                    
                }
        );
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'loadPageCache'), -1000);
        $eventManager->attach(MvcEvent::EVENT_RENDER, array($this, 'savePageCache'), -10001);
    }

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

    public function loadPageCache(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        if ($sm->has('Cache\File')) {
            $routeName = $e->getRouteMatch()->getMatchedRouteName();
            $fileCacheService = $sm->get('Cache\File');
            if ($fileCacheService->hasItem($routeName) && $fileCacheService->getItem($routeName)) {
                var_dump('using cache');
                $e->getResponse()->setContent($fileCacheService->getItem($routeName));
                return $e->getResponse();
            }
        }
    }

    public function savePageCache(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        if ($sm->has('Cache\File')) {
            var_dump('writting cache');
            $routeName = $e->getRouteMatch()->getMatchedRouteName();
            $fileCacheService = $sm->get('Cache\File');
            $fileCacheService->setItem($routeName, $e->getResponse()->getContent());
        }
    }

}
