# wanikani2anki

WaniKani -> Anki Exporter for the command line.

## Install

git clone this repository.
Make sure you have PHP installed. Recent PHP versions should do.
Make sure you have composer installed:
https://getcomposer.org/doc/00-intro.md#manual-installation

Run: composer install
Run: bin/import init

To export Kanji of a specific level, run:
bin/import kanji --level=8

To export leeches, run:
bin/import leeches

You should find kanji.csv or leeches.csv in your working directory.
To import it to Anki, please make sure you have "enable HTML" checked!
