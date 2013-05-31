<?php

namespace YZ\SupervisorBundle\Manager;

use Supervisor\Supervisor;

/**
 * SupervisorManager
 */
class SupervisorManager
{
    /**
     * @var array
     */
    private $supervisors = array();

    /**
     * Constuctor
     *
     * @param array $supervisorsConfiguration Configuration in the symfony parameters
     */
    public function __construct(array $supervisorsConfiguration)
    {
        foreach ($supervisorsConfiguration as $serverName => $configuration) {
            $supervisor = new Supervisor($serverName, $configuration['host'], $configuration['username'], $configuration['password'], $configuration['port']);
            $this->supervisors[$supervisor->getKey()] = $supervisor;
        }
    }

    /**
     * Get all supervisors
     *
     * @return Supervisor[]
     */
    public function getSupervisors()
    {
        return $this->supervisors;
    }

    /**
     * Get Supervisor by key
     *
     * @param string $key
     *
     * @return Supervisor|null
     */
    public function getSupervisorByKey($key)
    {
        if (isset($this->supervisors[$key])) {
            return $this->supervisors[$key];
        }

        return null;
    }
}
