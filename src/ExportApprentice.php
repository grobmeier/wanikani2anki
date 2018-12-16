<?php
namespace Grobmeier\WaniKani;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportApprentice extends Command
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
            ->setName('apprentice')
            ->setDescription('Exports apprentice items to an Anki readable file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new WaniKaniClient($output, $this->configuration);
        $result = $client->readApprenticeItems();

        $subjectIds = array_map(function($element) {
            return $element['subject_id'];
        }, $result);

        $client->readSubjects('apprentice.csv', $subjectIds);
    }
}
