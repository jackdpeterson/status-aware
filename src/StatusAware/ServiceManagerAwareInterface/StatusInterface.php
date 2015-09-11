<?php
namespace StatusAware\ServiceManagerAwareInterface;

interface StatusInterface
{

    /**
     *
     * @return array( 'component_name' => array(
     *         'is_critical' => true,
     *         'status' => 'up',
     *         'message' => ''
     *         )
     *         );
     */
    public function getServiceStatusAsArr();
}