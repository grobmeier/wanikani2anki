<?php
namespace Grobmeier\WaniKani\Anki;

class CardGenerator
{
    public function createCard($subject) {
        if ($subject->object == 'kanji') {
            return $this->createKanjiCard($subject);
        } else if ($subject->object == 'radical') {
            return $this->createRadicalCard($subject);
        } else if ($subject->object == 'vocabulary') {
            return $this->createVocabularyCard($subject);
        }
        return null;
    }

    public function createVocabularyCard($vocab) {
        $card = new Card();
        $card->question = '<div style="font-size: 40px;">' .
            $vocab->data->characters .
        '</div>';

        $readings = '';
        foreach ($vocab->data->readings as $reading) {
            $readings .= '<div style="font-size: 40px;">' . $reading->reading . '</div><br><br>';
        }

        $meanings = '';
        foreach ($vocab->data->meanings as $meaning) {
            $meanings .= $meaning->meaning . '<br>';
        }

        $card->answer = $readings . $meanings;
        $card->tags = 'vocab-level-' . $vocab->data->level;
        return $card;
    }

    public function createRadicalCard($radical) {
        $card = new Card();
        $card->question = '<div style="font-size: 40px;">' .
            $radical->data->characters .
            '</div>';

        $card->answer = $radical->data->meanings[0]->meaning;
        $card->tags = 'radical-level-' . $radical->data->level;
        return $card;
    }

    public function createKanjiCard($kanji) {
        $card = new Card();
        $readingString = '';

        foreach ($kanji->data->readings as $reading) {
            if ($reading->type == 'onyomi') {
                $readingString .= 'ON: ';
            } else {
                if ($reading->type == 'kunyomi') {
                    $readingString .= 'KUN: ';
                } else {
                    $readingString .= $reading->type . ': ';
                }
            }

            if ($reading->primary) {
                $readingString .= '<b>';
            }
            $readingString .= $reading->reading;
            if ($reading->primary) {
                $readingString .= '</b>';
            }

            $readingString .= '<br>';
        }

        $card->question =
            '<div style="font-size: 40px;">' .
                $kanji->data->characters .
            '</div>';

        $card->answer =
            $kanji->data->meanings[0]->meaning . '<br><br>' . $readingString;

        $card->tags = 'kanji-level-' . $kanji->data->level;
        return $card;
    }

}