<?php
namespace Grobmeier\WaniKani\Anki;

class Card
{
    public $question;
    public $answer;
    public $tags;


    public function toArray() {
        return [
            $this->question,
            $this->answer,
            $this->tags
        ];
    }
}