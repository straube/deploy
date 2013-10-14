<?php

namespace Straube\Deploy\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Straube\Deploy\Model\Config;

/**
 * Project list command.
 *
 * @author Gustavo Straube <gustavo@codekings.com.br>
 * @since 0.2
 */
class ProjectListCommand extends Command
{

    /**
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('project:list')
            ->setDescription('Project list command.');
    }

    /**
     * @see \Symfony\Component\Console\Command\Command::execute(InputInterface $input, OutputInterface $output)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getApplication()->getLongVersion()."\n");
        $output->writeln('<comment>Available projects:</comment>');

        $config = new Config();
        foreach ($config->getAvailableProjects() as $project) {
            $output->writeln(sprintf("\t- <info>%s</info>", $project));
        }

        return 0;
    }

}
