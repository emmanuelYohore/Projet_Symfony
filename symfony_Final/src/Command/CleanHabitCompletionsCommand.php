<?php

namespace App\Command;

use App\Service\HabitCompletionCleaner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clean-habit-completions',
    description: 'Clean habit completions based on their duration',
)]
class CleanHabitCompletionsCommand extends Command
{
    private HabitCompletionCleaner $cleaner;

    public function __construct(HabitCompletionCleaner $cleaner)
    {
        parent::__construct();
        $this->cleaner = $cleaner;
    }

    protected function configure()
    {
        $this
            ->setDescription('Clean habit completions based on their duration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        while (true) {
            $this->cleaner->clean();
            $io->success('Checked and cleaned habit completions.');

            sleep(3600);
        }

        return Command::SUCCESS;
    }
}