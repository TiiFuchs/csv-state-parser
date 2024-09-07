<?php

namespace Tii\CsvStateParser;

use Tii\CsvStateParser\Exceptions\InvalidStateException;

/**
 * @template TResult
 */
abstract class CsvStateParser
{
    protected string $state = 'start';

    private int $skipLines = 0;

    private bool $done = false;

    public function __construct(
        protected string $separator = ';',
        protected string $enclosure = '"',
        protected string $escape = '\\',
    ) {}

    /**
     * @return TResult
     *
     * @throws InvalidStateException
     */
    public function parse(string $filename): mixed
    {
        foreach ($this->rows($filename) as $row) {

            $stateMethod = 'state'.ucfirst($this->state);

            if (! method_exists($this, $stateMethod)) {
                throw new InvalidStateException("Method {$stateMethod} does not exist, but current state is {$this->state}.");
            }

            $this->$stateMethod($row);

        }

        return $this->result();
    }

    /**
     * @return TResult
     */
    abstract protected function result(): mixed;

    abstract protected function stateStart(array $row): void;

    /**
     * @return \Generator<array<int, string>>
     */
    protected function rows(string $filename): \Generator
    {
        $file = fopen($filename, 'r');

        while (! feof($file)) {
            if ($this->done) {
                break;
            }

            $row = fgetcsv($file, null, $this->separator, $this->enclosure, $this->escape);

            $row = array_map($this->mapValue(...), $row);

            if ($row === false) {
                continue;
            }

            if ($this->skipLines > 0) {
                $this->skipLines--;

                continue;
            }

            yield $row;
        }

        fclose($file);
    }

    protected function mapValue(string $value): string
    {
        return $value;
    }

    protected function skip(int $count): static
    {
        $this->skipLines = $count;

        return $this;
    }

    protected function done(): static
    {
        $this->done = true;

        return $this;
    }

    protected function state($nextState): static
    {
        if ($nextState instanceof \BackedEnum) {
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
