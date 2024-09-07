<?php

use Tii\CsvStateParser\Tests\NameParser;

afterEach(fn () => Mockery::close());

it('gets the first name from csv', function () {
    $result = (new NameParser)->parse(__DIR__.'/../files/people.csv');

    expect($result)->toBe('Robert Johnson');
});
