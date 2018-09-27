<?php
namespace Grobmeier\WaniKani;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Init extends Command
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
            ->setName('init')
            ->setDescription('Creates the api key file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Hello WK User.");
        $output->writeln("I am going to ask for your API key and store it to: " . $this->configuration['key_file']);
        $output->writeln("Please don't give away this key file or store your key on a machine which you do not own.");

        $helper = $this->getHelper('question');
        $question = new Question('Please enter your API v2 key: ');

        $apiKey = $helper->ask($input, $output, $question);

        if (empty($apiKey)) {
            $output->writeln("Please enter a key.");
            return;
        }

        $keyFile = $this->configuration['key_file'];
        if (file_exists($keyFile)) {
            unlink($keyFile);
        }

        file_put_contents($keyFile, $apiKey);

        $output->writeln("Thanks. All done.");
    }
}