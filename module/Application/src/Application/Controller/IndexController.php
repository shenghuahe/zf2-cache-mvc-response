<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        var_dump('not using cache');
        $someData = array('a' => 10, 'b' => 20);
        $this->getEventManager()->setIdentifiers(array(__CLASS__));
        $this->getEventManager()->trigger(__FUNCTION__.'.cache', $this, $someData);
        return new ViewModel();
    }

}
