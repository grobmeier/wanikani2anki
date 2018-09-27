<?php
namespace Grobmeier\WaniKani;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportKanji extends Command
{
    private $configuration;

    public function __construct($configuration)
    {
        parent::__construct();
        $this->configuration = $configuration;
    }

    protected function configure()
    {
        $this
            ->setName('kanji')
            ->setDescription('Exports Kanji to an Anki readable file')
            ->addOption(
                'level',
                null,
                InputOption::VALUE_REQUIRED,
                'The level to export',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $level = $input->getOption('level');

        $client = new WaniKaniClient($output, $this->configuration);
        $client->readKanji($level);
    }
}