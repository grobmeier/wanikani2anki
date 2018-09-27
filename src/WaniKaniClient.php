<?php

namespace Grobmeier\WaniKani;

use Grobmeier\WaniKani\Anki\CardGenerator;
use GuzzleHttp\Client;
use League\Csv\Writer;
use Symfony\Component\Console\Output\OutputInterface;

class WaniKaniClient
{
    private $configuration;
    private $client;
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output, $configuration)
    {
        $this->configuration = $configuration;
        $this->output = $output;

        $this->client = new Client([
            'base_uri' => $configuration['wanikani'],
            'timeout' => 5.0,
            'debug' => false,
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . $configuration['api_key']
            ]
        ]);
    }

    public function readSubjects($ids) {
        $response = $this->client->get($this->configuration['version'] . '/subjects?ids=' . implode(',', $ids));
        $result = \GuzzleHttp\json_decode($response->getBody());

        $writer = Writer::createFromPath('leeches.csv', 'w+');
        $cardGenerator = new CardGenerator();

        foreach ($result->data as $item) {
            $card = $cardGenerator->createCard($item);
            $writer->insertOne($card->toArray());
        }
        $this->output->writeln("Done.");
    }

    public function readLeeches() {

        $read = function($url = null) {
            if ($url == null) {
                $url = $this->configuration['version'] . '/review_statistics';
            } else {
                $prefix = 'https://api.wanikani.com';
                if (substr($url, 0, strlen($prefix)) == $prefix) {
                    $url = substr($url, strlen($prefix));
                }
            }

            $response = $this->client->get($url);
            $result = \GuzzleHttp\json_decode($response->getBody());

            $this->output->writeln("Reading...");
            return $result;
        };

        $leeches = [];
        $calculate = function($list) use (&$leeches) {
            foreach ($list as $result) {
                $element = $result->data;
                if ($element->meaning_incorrect > 0 || $element->reading_incorrect > 0) {
                    $scoreMeaning = $element->meaning_incorrect / $element->meaning_current_streak;
                    $scoreReading = $element->reading_incorrect / $element->reading_current_streak;

                    $max = max($scoreMeaning, $scoreReading);
                    if ($max > 2) {
                        array_push($leeches, [
                            "subject_id" => $element->subject_id,
                            "score" => max($scoreMeaning, $scoreReading),
                            "element" => $element
                        ]);
                    }
                }
            }
        };

        $result = $read();
        $calculate($result->data);
        while (!empty($result->pages->next_url)) {
            $result = $read($result->pages->next_url);
            $calculate($result->data);
        }

        return $leeches;
    }

    public function readKanji($level)
    {
        $response = $this->client->get($this->configuration['version'] . '/subjects?types=kanji&levels=' . $level);
        $result = \GuzzleHttp\json_decode($response->getBody());

        $writer = Writer::createFromPath('kanji.csv', 'w+');

        $cardGenerator = new CardGenerator();
        foreach ($result->data as $kanji) {
            $card = $cardGenerator->createKanjiCard($kanji);
            $writer->insertOne($card->toArray());
        }

        $this->output->writeln("Done.");
    }
}