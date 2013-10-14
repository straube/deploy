<?php

namespace Straube\Deploy\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Straube\Deploy\Model\LogQuery;

/**
 * Deploy log command.
 *
 * @author Gustavo Straube <gustavo@codekings.com.br>
 * @since 0.2
 */
class LogCommand extends Command
{

    /**
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('log')
            ->addArgument('project', InputArgument::REQUIRED, 'Project name.')
            ->addArgument('server', InputArgument::REQUIRED, 'Server name.')
            ->addArgument('count', InputArgument::OPTIONAL, 'Number of log records to display (10 records will be displayed if ommited).');
    }

    /**
     * @see \Symfony\Component\Console\Command\Command::execute(InputInterface $input, OutputInterface $output)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getArgument('project');
        $serverName = $input->getArgument('server');
        $count = $input->getArgument('count');

        $output->writeln($this->getApplication()->getLongVersion()."\n");
        $output->writeln(sprintf("<comment>Deploy history for '%s' server under '%s' project:</comment>", $serverName, $projectName));

        $logs = LogQuery::findLogs($projectName, $serverName, $count);

        if (empty($logs)) {
            $output->writeln("\tNo records found.");
        } else {
            $recordCount = 1;
            foreach ($logs as $log) {
                $output->writeln(sprintf("\n\t- Record %d\n\t\t   User: <info>%s</info>\n\t\tCommits: <info>%s</info> --> <info>%s</info>\n\t\t   Date: <info>%s</info>", $recordCount, $log->getUser(), $log->getFrom(), $log->getTo(), $log->getDate()));
                $recordCount++;
            }
        }

        return 0;
    }

}
