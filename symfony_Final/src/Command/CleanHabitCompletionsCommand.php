<?php

namespace App\Command;

use App\Service\HabitCompletionCleaner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Psr\Log\LoggerInterface;

#[AsCommand(
    name: 'app:clean-habit-completions',
    description: 'Clean habit completions based on their duration',
)]
class CleanHabitCompletionsCommand extends Command
{
    private HabitCompletionCleaner $cleaner;
    private LoggerInterface $logger;

    public function __construct(HabitCompletionCleaner $cleaner, LoggerInterface $logger)
    {
        parent::__construct();
        $this->cleaner = $cleaner;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setDescription('Clean habit completions based on their duration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

            $this->logger->info('Starting process to clean habit completions.');
            $this->cleaner->clean();
            $this->logger->info('Process succeeded.');

            $io->success('Checked and cleaned habit completions.');


        return Command::SUCCESS;
    }
}