<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/StatusAware for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace StatusAware\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use StatusAware;
use StatusAware\ServiceManagerAwareInterface\StatusInterface;

class StatusController extends AbstractActionController
{

    public function indexAction()
    {
        
        // we need to init EACH factory to ensure it gets initialized into the statusmanager.
        $factoryList = $this->getServiceLocator()->get('Config')['service_manager']['factories'];
        
        foreach ($factoryList as $key => $value) {
            $noLoad = false;
            $blacklist = array(
                'ZF',
                'AcMailer',
                'AssetManager',
                'SlmQueue',
                'StatusAwareManager',
                'doctrine'
            );
            foreach ($blacklist as $blKey => $blacklistItem) {
                if (strpos(strtolower($key), strtolower($blacklistItem)) !== false) {
                    $noLoad = true;
                    break;
                }
            }
            
            if ($noLoad == false) {
                // echo "Loading " . $key . "\n";
                try {
                    $factory = $this->getServiceLocator()->get($key);
                    if ($factory instanceof StatusInterface) {
                        $status = $factory->getServiceStatusAsArr();
                    }
                } catch (\Exception $e) {
                    //echo $e->getMessage();
                    //die();
                }
            }
        }
        
        //die();
        /**
         *
         * @var $statusAwareManager StatusManager
         */
        $statusAwareManager = $this->getServiceLocator()->get('StatusAwareManager');
        $viewModel = new JsonModel();
        $viewModel->setVariables($statusAwareManager->getStatus());
        
        return $viewModel;
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /default/default/foo
        return array();
    }
}
