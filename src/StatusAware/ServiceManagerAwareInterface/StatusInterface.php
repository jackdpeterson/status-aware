<?php
namespace StatusAware\ServiceManagerAwareInterface;

interface StatusInterface
{

    /**
     *
     * @return array( 
     *         'name' => 'example-service',
     *         'is_critical' => true,
     *         'status' => 'up',
     *         'message' => ''
     *         );
     */
    public function getServiceStatusAsArr();
}
