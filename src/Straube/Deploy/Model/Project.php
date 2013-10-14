<?php

namespace Straube\Deploy\Model;

class Project
{

    /**
     *
     * @var string 
     */
    private $name;
    
    /**
     *
     * @var string 
     */
    private $repo;

    /**
     *
     * @var array 
     */
    private $servers;

    /**
     * 
     * @param string $name
     * @param array $config
     */
    public function __construct($name, array $config)
    {
        $this->name = $name;
        if (!isset($config['repo']) || !isset($config['servers'])) {
            throw new \RuntimeException(sprintf("Invalid configuration for '%s' project. Repository or servers configuration is missing.", $name));
        }
        $this->repo = new Repo($config['repo']);
        $this->servers = array();
        foreach ($config['servers'] as $serverName => $serverConfig) {
            $this->servers[$serverName] = new Server($serverName, $serverConfig);
        }
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 
     * @return \Straube\Deploy\Model\Repo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * 
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * 
     * @param string $serverName
     * @return \Straube\Deploy\Model\Server
     */
    public function getServer($serverName)
    {
        $server = null;
        if ($this->hasServer($serverName)) {
            $server = $this->servers[$serverName];
        }

        return $server;
    }

    /**
     * 
     * @param string $serverName
     * @return boolean
     */
    public function hasServer($serverName)
    {
        return isset($this->servers[$serverName]);
    }

}
