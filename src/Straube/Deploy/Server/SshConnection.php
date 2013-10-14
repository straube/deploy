<?php

namespace Straube\Deploy\Server;

use Symfony\Component\Process\Process;

class SshConnection extends AbstractConnection
{

    /**
     * {@inheritDoc}
     */
    public function sendFile($path)
    {
        $server = $this->getServer();
        $dir = dirname($path);
        $dirInServer = $server->getPath().$dir;
        $this->doRunCommand(sprintf("ssh %s@%s '[ ! -f \"%s\" ] && mkdir -p %s'", $server->getUser(), $server->getHost(), $dirInServer, $dirInServer));

        return $this->doRunCommand(sprintf("scp -r %s %s@%s:%s", $path, $server->getUser(), $server->getHost(), $server->getPath().$path), $this->getOriginPath());
    }

    /**
     * {@inheritDoc}
     */
    public function removeFile($path)
    {
        $server = $this->getServer();

        return $this->doRunCommand(sprintf("ssh %s@%s 'rm -rf %s'", $server->getUser(), $server->getHost(), $server->getPath().$path));
    }

    /**
     * {@inheritDoc}
     */
    public function runCommand($command, $cwd = null)
    {
        $server = $this->getServer();
        $remoteCommand = sprintf("ssh -t %s@%s '%s && exit'", $server->getUser(), $server->getHost(), $command);

        return $this->doRunCommand($remoteCommand, $cwd);
    }

    /**
     * 
     * @param string $command
     * @param string $cwd
     * @return boolean
     */
    private function doRunCommand($command, $cwd = null)
    {
        $process = new Process($command, $cwd);
        $process->setTimeout(60);
        $process->run();

        return $process->isSuccessful();
    }

}
