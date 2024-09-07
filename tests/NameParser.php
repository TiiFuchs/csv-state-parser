<?php

namespace Tii\CsvStateParser\Tests;

use Tii\CsvStateParser\CsvStateParser;

class NameParser extends CsvStateParser
{
    protected ?string $name = null;

    protected function stateStart(array $row): void
    {
        $this->state('next');
    }

    protected function stateNext(array $row): void
    {
        $this->name = $row[0].' '.$row[1];

        $this->state('finish');
    }

    protected function stateFinish(array $row): void {}

    public function result(): mixed
    {
        return $this->name;
    }
}
