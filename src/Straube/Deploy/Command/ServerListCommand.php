<?php

namespace Straube\Deploy\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument('name', InputArgument::OPTIONAL, 'Random name.');
    }

    /**
     * @see \Symfony\Component\Console\Command\Command::execute(InputInterface $input, OutputInterface $output)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Hello world...</comment>');

        $name = $input->getArgument('name');
        if (!empty($name)) {
            $output->writeln(sprintf('Random name: <info>%s</info>', $name));
        }

        return 0;
    }

}
