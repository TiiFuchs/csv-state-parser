<?php

namespace Tii\CsvStateParser;

use Tii\CsvStateParser\Exceptions\InvalidStateException;

abstract class CsvStateParser
{
    protected string $state = 'start';

    public function __construct(
        protected string $separator = ';',
        protected string $enclosure = '"',
        protected string $escape = '\\',
    ) {}

    /**
     * @throws InvalidStateException
     */
    public function parse(string $filename): mixed
    {
        foreach ($this->rows($filename) as $row) {

            $stateMethod = 'state'.ucfirst($this->state);
            var_dump($stateMethod);

            if (! method_exists($this, $stateMethod)) {
                throw new InvalidStateException("Method {$stateMethod} does not exist, but current state is {$this->state}.");
            }

            $this->$stateMethod($row);

        }

        return $this->result();
    }

    abstract public function result(): mixed;

    abstract protected function stateStart(array $row);

    /**
     * @return \Generator<array>
     */
    protected function rows(string $filename): \Generator
    {
        $file = fopen($filename, 'r');

        while (! feof($file)) {
            yield fgetcsv($file, null, $this->separator, $this->enclosure, $this->escape);
        }

        fclose($file);
    }

    protected function state(string $nextState): static
    {
        $this->state = $nextState;

        return $this;
    }
}
