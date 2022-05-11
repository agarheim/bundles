<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Command;

use App\Analytics\RetailCustomerHistoryBundle\Controller\CronCommandController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CronGetHistoryCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:run-get-history';
    protected $cronCommandController;

    public function __construct(CronCommandController $cronCommandController)
    {
        $this->cronCommandController = $cronCommandController;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // краткое описание, отображающееся при запуске "php bin/console list"
            ->setDescription('Get history from RetailCRM.')
            ->addArgument('variantId', InputArgument::REQUIRED, 'The variantId for start settings.')
            // полное описание команды, отображающееся при запуске команды
            // с опцией "--help"
            ->setHelp('Получение истории изменений из RetailCRM...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
//        $input->getArguments();
//        $input->getArgument('username');

        if ($moduleSettings = $this->cronCommandController->getSettings($input->getArgument('variantId'))){
            $output->writeln('Go!');
            $this->cronCommandController->startCRM($moduleSettings);
            $output->writeln('Finish!');
            return Command::SUCCESS;
        }

        $output->writeln('Ух ты!');
        return Command::FAILURE;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}