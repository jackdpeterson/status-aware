<?php
namespace StatusAware\Service;

use StatusAware\ServiceManagerAwareInterface\StatusInterface;

class StatusManager
{

    protected $_statusReportingClassList;

    protected $_statusResponseCollection;

    public function __construct()
    {
        $this->_statusReportingClassList = array();
        $this->_statusResponseCollection = array();
    }

    /**
     *
     * @param StatusInterface $class            
     * @return \StatusAware\Factory\StatusManager
     */
    public function addStatusReportingClass(StatusInterface $class)
    {
        $this->_statusReportingClassList[] = $class;
        return $this;
    }

    /**
     *
     * @param unknown $className            
     * @return \StatusAware\Factory\StatusManager
     */
    public function removeStatusReportingClass($className)
    {
        $updatedClassList = array();
        foreach ($this->_statusReportingClassList as $key => $classObj) {
            if (get_class($classObj) == $className) {
                // do not add back in
            } else {
                $updatedClassList[] = $classObj;
            }
        }
        
        $this->_statusReportingClassList = $updatedClassList;
        return $this;
    }

    protected function loadStatusResponses()
    {
        if (count($this->_statusResponseCollection) == 0) {
            $out = array();
            foreach ($this->_statusReportingClassList as $key => $classObj) {
                try {
                    $response = $classObj->getServiceStatusAsArr();
                    $out[] = $response;
                } catch (\Exception $e) {
                    $out[] = array(
                        'status' => 'down',
                        'message' => 'Exception detected while fetching status: ' . $e->getMessage()
                    );
                }
            }
            $this->_statusResponseCollection = $out;
        }
        return $this->_statusResponseCollection;
    }

    /**
     *
     * @abstract determines if any critical services are down
     * @return bool
     */
    protected function AreAnyCriticalServicesDown()
    {
        foreach ($this->_statusResponseCollection as $key => $classObjResponse) {
            if (array_key_exists('is_critical', $classObjResponse) && $classObjResponse['is_critical'] == true) {
                if (array_key_exists('status', $classObjResponse) && strtolower($classObjResponse['status']) == 'down') {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     *
     * @abstract determines if any critical services are degraded
     * @return bool
     */
    protected function areAnyCriticalServicesDegraded()
    {
        foreach ($this->_statusResponseCollection as $key => $classObjResponse) {
            if (array_key_exists('is_critical', $classObjResponse) && $classObjResponse['is_critical'] == true) {
                if (array_key_exists('status', $classObjResponse) && strtolower($classObjResponse['status']) == 'degraded') {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     *
     * @abstract determines if any services are down
     * @return bool
     */
    protected function areAnyServicesDown()
    {
        foreach ($this->_statusResponseCollection as $key => $classObjResponse) {
            if (array_key_exists('status', $classObjResponse) && strtolower($classObjResponse['status']) === 'down') {
                return true;
            }
        }
        
        return false;
    }

    /**
     *
     * @abstract determines if any services are degraded
     * @return bool
     */
    protected function areAnyServicesDegraded()
    {
        foreach ($this->_statusResponseCollection as $key => $classObjResponse) {
            if (array_key_exists('status', $classObjResponse) && strtolower($classObjResponse['status']) === 'degraded') {
                return true;
            }
        }
        
        return false;
    }

    protected function getComponentStatusAsArray()
    {
        $out = array();
        
        foreach ($this->_statusReportingClassList as $key => $classObj) {
            if ($classObj instanceof StatusInterface) {
                $response = $classObj->getServiceStatusAsArr();
                if (! is_array($response)) {
                    $response = array();
                }
                $out[] = $response;
            }
        }
        
        return $out;
    }

    /**
     *
     * @abstract gets the status of services that implement the StatusInterface
     * @return array
     */
    public function getStatus()
    {
        $this->loadStatusResponses();
        $responseArray = array(
            'status' => 'up',
            'components' => array(),
            'message' => ''
        );
        
        if ($this->areAnyServicesDegraded()) {
            $responseArray['status'] = 'degraded';
            $responseArray['message'] = 'at least one service is degraded.';
        }
        
        if ($this->areAnyServicesDown()) {
            $responseArray['status'] = 'degraded';
            $responseArray['message'] = 'at least one service is down.';
        }
        
        if ($this->areAnyCriticalServicesDegraded()) {
            $responseArray['status'] = 'degraded';
            $responseArray['message'] = 'at least one critical service is degraded.';
        }
        
        if ($this->areAnyCriticalServicesDown()) {
            $responseArray['status'] = 'down';
            $responseArray['message'] = 'at least one critical service is down.';
        }
        
        $responseArray['components'] = $this->getComponentStatusAsArray();
        
        return $responseArray;
    }
}
