<?php

namespace Tii\CsvStateParser;

use Tii\CsvStateParser\Exceptions\InvalidStateException;

abstract class CsvStateParser
{
    protected string $state = 'start';

    private int $skipLines = 0;

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
            $row = fgetcsv($file, null, $this->separator, $this->enclosure, $this->escape);

            if ($this->skipLines > 0) {
                $this->skipLines--;

                continue;
            }

            yield $row;
        }

        fclose($file);
    }

    protected function skip(int $count): static
    {
        $this->skipLines = $count;
    }

    protected function state(string|\StringBackedEnum $nextState): static
    {
        if ($nextState instanceof \StringBackedEnum) {
            $nextState = $nextState->value;
        }

        $nextState = (string) $nextState;

        if (empty($nextState)) {
            throw new InvalidStateException('Passed next state is not a valid type.');
        }

        $this->state = $nextState;

        return $this;
    }
}
