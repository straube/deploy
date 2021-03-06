<?php

namespace Straube\Deploy\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Straube\Deploy\Model\Config;

/**
 * Server list command.
 *
 * @author Gustavo Straube <gustavo@codekings.com.br>
 * @since 0.2
 */
class ServerListCommand extends Command
{

    /**
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('server:list')
            ->setDescription('Server list command.')
            ->addArgument('project', InputArgument::REQUIRED, 'Project name.');
    }

    /**
     * @see \Symfony\Component\Console\Command\Command::execute(InputInterface $input, OutputInterface $output)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getArgument('project');

        $output->writeln($this->getApplication()->getLongVersion()."\n");
        $output->writeln(sprintf("<comment>Available servers under '%s' project:</comment>", $projectName));
        
        $config = new Config();
        $project = $config->getProject($projectName);
        
        if (null === $project) {
            throw new \Exception(sprintf("Project '%s' not found.", $projectName));
        }
        
        foreach ($project->getServers() as $server) {
            $output->writeln(sprintf("\t- <info>%s</info>", $server->getName()));
        }

        return 0;
    }

}
