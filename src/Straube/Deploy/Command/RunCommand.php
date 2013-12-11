<?php

namespace Straube\Deploy\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Gitter\Client;

use Straube\Deploy\Model\Config;
use Straube\Deploy\Model\Log;
use Straube\Deploy\Model\LogQuery;
use Straube\Deploy\Server\ConnectionFactory;

/**
 * Run deploy command.
 *
 * @author Gustavo Straube <gustavo@codekings.com.br>
 * @since 0.2
 */
class RunCommand extends Command
{

    /**
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run deploy command.')
            ->addArgument('project', InputArgument::REQUIRED, 'Project name.')
            ->addArgument('server', InputArgument::REQUIRED, 'Server name.')
            ->addArgument('from', InputArgument::REQUIRED, 'Commit from.')
            ->addArgument('to', InputArgument::OPTIONAL, 'Commit to (HEAD commit will used if ommited).');
    }

    /**
     * @see \Symfony\Component\Console\Command\Command::execute(InputInterface $input, OutputInterface $output)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getArgument('project');
        $serverName = $input->getArgument('server');
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');

        $output->writeln($this->getApplication()->getLongVersion()."\n");

        $config = new Config();
        $project = $config->getProject($projectName);
        if (null == $project) {
            throw new \Exception(sprintf("No '%s' project found.", $projectName));
        }
        $server = $project->getServer($serverName);
        if (null == $server) {
            throw new \Exception(sprintf("No '%s' server found under '%s' project.", $serverName, $projectName));
        }

        $lastDeploy = null;
        $logs = LogQuery::findLogs($projectName, $serverName, 1);
        if (!empty($logs)) {
            $lastDeploy = reset($logs);
        }

        $filesToSend = array();
        $filesToRemove = array();

        if ('last' == $from) {
            if (null === $lastDeploy) {
                throw new \Exception("Since no deploy was made until now, you cannot use 'last' option.");
            }
            $from = $lastDeploy->getTo();
        }

        $git = new Client();
        $repository = null;

        $homePath = realpath($_SERVER['HOME']);
        $cachePath = $homePath.'/.cache/deploy/'.$projectName.'/'.$serverName.'/';
        if (!is_dir($cachePath)) {
            @mkdir(dirname($cachePath), 0755, true); // Using dirname, because repository path itself is created by Gitter API.
            $repository = $git->createRepository($cachePath);
            $git->run($repository, sprintf('remote add origin %s', $project->getRepo()->getUrl()));
        } else {
            $repository = $git->getRepository($cachePath);
        }

        $output->writeln('Updating the repository cache. This may take a while ...');

        $git->run($repository, 'remote update');

        $repository->checkout($server->getBranch())->pull();

        if (empty($to)) {
            $to = $git->run($repository, 'rev-parse HEAD');
        }

        $repository->checkout($to);

        $output->writeln('Getting files list.');

        if ('init' == $from) {
            $cmdOutput = $git->run($repository, sprintf('ls-tree --name-only --full-tree -r %s', $to));
            $filesToSend = explode("\n", trim($cmdOutput));
        } else {
            $cmdOutput = $git->run($repository, sprintf('diff --name-status %s %s', $from, $to));
            $diff = explode("\n", trim($cmdOutput));
            foreach ($diff as $file) {
                $matches = array();
                if (preg_match('/^([A-Z])\s+(.*)$/i', $file, $matches)) {
                    'D' == $matches[1]
                        ? ($filesToRemove[] = $matches[2])
                        : ($filesToSend[] = $matches[2]);
                }
            }
        }

        $connection = ConnectionFactory::getConnection($server, $cachePath);

        $preDeployCommads = $server->getPreDeployCommands();
        if (!empty($preDeployCommads)) {
            $output->writeln("\n<comment>Running pre deploy commands:</comment>");
            foreach ($preDeployCommads as $command) {
                $output->write(sprintf("\t- %s ... ", $command));
                $ok = $connection->runCommand($command, $cachePath);
                $output->writeln(sprintf("%s", ($ok ? '<info>OK</info>' : '<error>Error</error>')));
            }
        }

        if (!empty($filesToSend)) {
            $output->writeln("\n<comment>Sending (creating/updating) files:</comment>");
            foreach ($filesToSend as $file) {
                $ok = $connection->sendFile($file);
                $output->writeln(sprintf("\t- %s %s", $file, ($ok ? '<info>OK</info>' : '<error>Error</error>')));
            }
        }

        if (!empty($filesToRemove)) {
            $output->writeln("\n<comment>Removing files:</comment>");
            foreach ($filesToRemove as $file) {
                $ok = $connection->removeFile($file);
                $output->writeln(sprintf("\t- %s %s", $file, ($ok ? '<info>OK</info>' : '<error>Error</error>')));
            }
        }

        $postDeployCommands = $server->getPostDeployCommands();
        if (!empty($postDeployCommands)) {
            $output->writeln("\n<comment>Running post deploy commands:</comment>");
            foreach ($postDeployCommands as $command) {
                $output->write(sprintf("\t- %s ... ", $command));
                $ok = $connection->runCommand($command, $cachePath);
                $output->writeln(sprintf("%s", ($ok ? '<info>OK</info>' : '<error>Error</error>')));
            }
        }

        $user = posix_getpwuid(posix_geteuid());
        $log = new Log(array(
            'project' => $project->getName(),
            'server' => $server->getName(),
            'from' => $from,
            'to' => $to,
            'user' => $user['name'],
        ));
        $log->save();

        return 0;
    }

}
