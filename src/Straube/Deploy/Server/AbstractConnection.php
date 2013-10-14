<?php

namespace Straube\Deploy\Server;

use Straube\Deploy\Model\Server;

abstract class AbstractConnection
{

    /**
     *
     * @var \Straube\Deploy\Model\Server
     */
    private $server;

    /**
     *
     * @var string
     */
    private $originPath;

    /**
     *
     * @param \Straube\Deploy\Model\Server $server
     * @param string $originPath
     */
    public function __construct(Server $server, $originPath)
    {
        $this->server = $server;
        $this->originPath = $originPath;
    }

    /**
     *
     * @return \Straube\Deploy\Model\Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     *
     * @return string
     */
    public function getOriginPath()
    {
        return $this->originPath;
    }

    /**
     *
     * @param string $path
     * @return boolean
     */
    public abstract function sendFile($path);

    /**
     *
     * @param string $path
     * @return boolean
     */
    public abstract function removeFile($path);

    /**
     *
     * @param string $command
     * @param string $cwd
     * @return boolean
     */
    public abstract function runCommand($command, $cwd = null);

}
