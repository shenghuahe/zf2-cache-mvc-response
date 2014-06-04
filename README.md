Example of caching MVC response using Firesystem cache in ZF2
==

**Configuration**

The filesystem cache is configured within Application/module.config.php and therefore deligated to Zend\Cache\Service\StorageCacheAbstractServiceFactory to construct the Cache Adapter. 

**Response Caching**

The caching for the MVC response is done through event listeners. This is for separating the concerns and making the code much better decoupled and reusable. 

Check out the two methods *loadPageCache()* and *savePageCache()* within Application/Module.php

savePageCache() is attached to the MvcEvent::EVENT_RENDER event with a very low priority. This makes sure $e->getResponse()->getContent() is populated before adding it to the cache. 

loadPageCache() is attached to the MvcEvent::EVENT_ROUTE event with a low priority. This allows all other attached events to run first before loading the response data cache. If the response data is within the cache, $e->getResponse()->setContent() will be called and the response object will be returned. This stops all subsequent listeners attached to the same event from executing. You might wonder why the savePageCache() method no longer gets ran either, and that's attached to a different event (EVENT_RENDER). The trick is actually done within \Zend\Mvc\Application::run() by the following block of code:

	$result = $events->trigger(MvcEvent::EVENT_ROUTE, $event, $shortCircuit);
        if ($result->stopped()) {
            $response = $result->last();
            if ($response instanceof ResponseInterface) {
                $event->setTarget($this);
                $event->setResponse($response);
                $events->trigger(MvcEvent::EVENT_FINISH, $event);
                $this->response = $response;
                return $this;
            }
        }

You can see the $result->stopped() returns true in this case and the $result object is an instance of Zend\EventManager\ResponseCollection. The last result is the response object with the data retrieved from the cache!
