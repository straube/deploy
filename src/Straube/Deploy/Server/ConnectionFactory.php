<?php

namespace Straube\Deploy\Server;

use Straube\Deploy\Model\Server;

class ConnectionFactory
{

    /**
     * 
     * @param \Straube\Deploy\Model\Server $server
     * @param string $originPath
     * @return \Straube\Deploy\Server\AbstractConnection
     */
    public static function getConnection(Server $server, $originPath)
    {
        $connection = null;
        switch ($server->getType()) {
            case 'ssh':
                $connection = new SshConnection($server, $originPath);
        }

        return $connection;
    }

    /**
     *
     * @return array
     */
    public final static function getAvailableTypes()
    {
        return array(
            'ssh',
        );
    }

}
